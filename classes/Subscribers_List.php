<?php

namespace letterpress;

//Include WP_List_Table if it's not already
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Subscribers_List extends \WP_List_Table {

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'subscriber',     //singular name of the listed records
            'plural'    => 'subscribers',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'status':
                $status = $item[$column_name];
                $class = '';
                switch ($status){
                    case 'S':
                        $class = 'bg-success';
                        break;
                    case 'U':
                        $class = 'bg-secondary';
                        break;
                    case 'B':
                        $class = 'bg-danger';
                        break;
                }
                return "<span class='status-context {$class}'>{$status}</span>";
            case 'name':
            case 'email':
            case 'wp_user_id':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    function column_title($item){
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['name'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'name'     => 'Name',
            'email'    => 'Email',
            'status'  => 'Status',
            'wp_user_id'  => 'WP User ID',
        );
        return $columns;
    }


    function get_bulk_actions() {
        $actions = array(
            'subscribe'    => __('Subscribe', 'letterpress'),
            'unsubscribe'    => __('UnSubscribe', 'letterpress'),
            'block'    => __('Block', 'letterpress'),
            'delete'    => __('Delete', 'letterpress'),
        );
        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        global $wpdb;

        $subscriber = null;
        if (isset($_POST['subscriber'])){
            $subscriber = implode(',', $_POST['subscriber']);
        }
		$action = $this->current_action();

        //Detect when a bulk action is being triggered...
	    if ( $subscriber ) {
		    if ( 'delete' === $action ) {
			    $wpdb->query( $wpdb->prepare( "DELETE from {$wpdb->prefix}lp_subscribers WHERE id IN (%s);", $subscriber ) );
		    }
		    if ( 'block' === $action ) {
			    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}lp_subscribers SET status='B' WHERE id IN(%s) ;", $subscriber ) );
		    }
		    if ( 'subscribe' === $action ) {
			    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}lp_subscribers SET status='S' WHERE id IN(%s) ;", $subscriber ) );
		    }
		    if ( 'unsubscribe' === $action ) {
			    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}lp_subscribers SET status='U' WHERE id IN(%s) ;", $subscriber ) );
		    }
	    }
    }

    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        $total_items = $wpdb->get_var("select COUNT(id) from {$wpdb->prefix}lp_subscribers");

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 50;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $current_page = $this->get_pagenum();

        //$data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $offset = ($current_page-1)*$per_page;

        //Check for search
        $subscriberQuery = "select * from {$wpdb->prefix}lp_subscribers ";
        if (isset($_POST['s'])){
            $searchTerm = sanitize_text_field($_POST['s']);
	        $subscriberQuery = $wpdb->prepare(
		        " WHERE name LIKE %s OR email LIKE %s ",
		        '%' . $wpdb->esc_like($searchTerm) . '%',
		        '%' . $wpdb->esc_like($searchTerm) . '%'
	        );
        }

        $subscribers = $wpdb->get_results($subscriberQuery . $wpdb->prepare(
		        " ORDER BY id DESC LIMIT %d, %d ;",
		        $offset,
		        $per_page
	        ), ARRAY_A );

        $this->items = $subscribers;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}