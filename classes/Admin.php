<?php
namespace letterpress;

class Admin{
    public function __construct(){
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        //White list for style
        add_action('safe_style_css', array($this, 'safe_style_css'));

        add_action('admin_action_lp_save_campaign', array($this, 'save_campaign_post'));
        add_action('admin_action_lp_edit_campaign', array($this, 'edit_campaign_post'));
        add_action('admin_init', array($this, 'register_settings'));

        add_action('admin_action_lp_campaign_control', array($this, 'campaign_control'));
        add_action('admin_action_lp_sync_users', array($this, 'sync_users'));
        add_action('admin_action_lp_test_campaign', array($this, 'lp_test_campaign'));
    }

    public function add_admin_menu(){
        add_menu_page('LetterPress', 'LetterPress', 'manage_options', 'letterpress', array($this, 'letterpress_settings_page'), 'dashicons-email-alt', 30);

        add_submenu_page('letterpress','Campaigns', 'Campaigns', 'manage_options', 'letterpress-campaigns', array($this, 'letterpress_campaigns'));
        add_submenu_page('letterpress','Subscribers', 'Subscribers', 'manage_options', 'letterpress-subscribers', array($this, 'letterpress_subscribers'));

        add_submenu_page('letterpress','Settings', 'Settings', 'manage_options', 'letterpress-settings', array($this, 'letterpress_settings'));
        add_submenu_page('letterpress','Cron Settings', 'Cron Job', 'manage_options', 'letterpress-cron', array($this, 'letterpress_cron'));
    }

    public function admin_enqueue_scripts(){
        wp_enqueue_style('lp-admin-style', LPRESS()->url.'assets/css/admin.css', array(), LPRESS_VERSION );
    }

    public function register_settings(){
        register_setting( 'lp_option', 'lp_option', array($this, 'sanitize' ) );
    }

    public function safe_style_css($styles){
        $styles[] = 'display';
        $styles[] = 'font-family';
        return $styles;
    }

	//Sanitize each array item
	public function sanitize($options){
		foreach ($options as $key => $value){
			if (is_array($value)){
				$options[$key] = $this->sanitize($value);
			}else{
				$options[$key] = sanitize_text_field($value);
			}
		}
		return $options;
	}

    public function letterpress_settings_page(){
        $this->load_view();
    }

    public function letterpress_campaigns(){
        $this->load_view();
    }

    public function letterpress_settings(){
        $this->load_view();
    }

    public function letterpress_subscribers(){
        $this->load_view();
    }

    public function letterpress_cron(){
        $this->load_view();
    }

    /**
     * Load administrative views
     */
    public function load_view() {
	    $page = 'letterpress.php';
	    if ( isset( $_GET['page'] ) ) {
		    $page = sanitize_text_field( $_GET['page'] );
	    }
	    if ( isset( $_GET['sub_page'] ) ) {
		    $page = sanitize_text_field( $_GET['sub_page'] );
	    }
	    include LPRESS_PATH . "views/{$page}.php";
    }

    public function save_campaign_post() {
	    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'letterpress_add_campaign' ) ) {
		    exit( 'Not Verified' );
	    }

	    global $wpdb;

	    $campaign_page_url = admin_url( 'admin.php?page=letterpress-campaigns' );

	    $campaign_data = array();
	    $campaign_data['name']    = sanitize_text_field( $_POST['name'] );
	    $campaign_data['subject'] = sanitize_text_field( $_POST['subject'] );
	    $campaign_data['message'] = lp_sanitize_html( $_POST['message'] );
	    $campaign_data['slug']    = LPRESS()->h->random_str();

	    $wpdb->insert( $wpdb->prefix . 'lp_campaigns', $campaign_data );

	    wp_safe_redirect( $campaign_page_url );
    }

    public function edit_campaign_post() {
	    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'letterpress_add_campaign' ) ) {
		    exit( 'Not Verified' );
	    }

	    global $wpdb;

	    $campaign_id = sanitize_text_field( $_POST['campaign_id'] );

	    $campaign_data            = array();
	    $campaign_data['name']    = sanitize_text_field( $_POST['name'] );
	    $campaign_data['subject'] = sanitize_text_field( $_POST['subject'] );
	    $campaign_data['message'] = lp_sanitize_html( $_POST['message'] );

	    $wpdb->update( $wpdb->prefix . 'lp_campaigns', $campaign_data, array( 'id' => $campaign_id ) );

	    wp_safe_redirect( wp_get_referer() );
    }

    public function campaign_control(){
        global $wpdb;
        $campaign_page_url = admin_url( 'admin.php?page=letterpress-campaigns' );

        if (isset($_GET['campaign_id']) && isset($_GET['status']) ){
            $campaign_id = sanitize_text_field($_GET['campaign_id']);
            $status = sanitize_text_field($_GET['status']);

            if ($status === 'delete'){
                $wpdb->delete(
                    $wpdb->prefix."lp_campaigns",
                    array('id' => $campaign_id)
                );
            }else{
                $is_update = $wpdb->update(
                    $wpdb->prefix."lp_campaigns",
                    array('status' => $status),
                    array('id' => $campaign_id)
                );
            }
        }

        wp_redirect($campaign_page_url);
    }

    public function sync_users(){
        global $wpdb;

        $lastSyncWPUserID = (int) $wpdb->get_var(" select MAX(wp_user_id) as lastSyncWPUserID from {$wpdb->prefix}lp_subscribers ");
        $wp_users = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}users WHERE ID > %d ", $lastSyncWPUserID ) );

        if ( ! empty($wp_users)){
            foreach ($wp_users as $user){
                //checking duplicate
                $duplicate_query = $wpdb->get_row("select * from {$wpdb->prefix}lp_subscribers WHERE email = '{$user->user_email}' ");

                if (!$duplicate_query){
                    $token = LPRESS()->h->random_str();
                    $subscriber_data = array(
                        'name' => $user->display_name,
                        'email' => $user->user_email,
                        'token' => $token,
                        'status' => 'S',
                        'wp_user_id' => $user->ID,
                    );
                    $wpdb->insert(
                        "{$wpdb->prefix}lp_subscribers",
                        $subscriber_data
                    );
                }

            }
        }
        //die();

        wp_safe_redirect(admin_url( 'admin.php?page=letterpress-subscribers' ));
    }

    /**
     * @since v.1.0.0
     *
     * Send a test campaign to check everything is working
     */

    public function lp_test_campaign() {
	    $campaign_page_url = admin_url( 'admin.php?page=letterpress-campaigns' );
	    $campaign_id       = sanitize_text_field( $_GET['campaign_id'] );
	    if ( isset( $_GET['campaign_id'] ) ) {
		    global $wpdb;

		    $campaign = $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}lp_campaigns WHERE id = %d limit 1; ", absint( $campaign_id ) ) );

		    if ( $campaign ) {
			    $toEmail = LPRESS()->h->get_option( 'test_campaign_email', get_option( 'admin_email' ) );
			    $cron    = new Cron();

			    $message = apply_filters( 'lp_email_message', $cron->get_the_campaign_message( $campaign ) );

			    $fromEmail = LPRESS()->h->from_email();
			    $fromName  = LPRESS()->h->from_name();
			    $headers   = array( 'Content-Type: text/html; charset=UTF-8', "From: {$fromName} <{$fromEmail}>" );

			    //Send Now
			    wp_mail( $toEmail, stripslashes( $campaign->subject ), $message, $headers );
		    }
	    }

	    wp_safe_redirect( $campaign_page_url );
    }
}