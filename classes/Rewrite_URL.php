<?php
namespace letterpress;

class Rewrite_URL{
    public function __construct(){
        add_action('init', array($this, 'rewrite_rule'), 10, 0);
        add_filter('query_vars', array($this, 'register_query_vars') );
        add_filter('template_include', array($this, 'template_include_view_campaign') );
        add_filter('template_include', array($this, 'template_include_unsubscribe') );

        //Auto Save Rewrite Rules
        add_action('admin_init', array($this, 'flush_rewrite_rules'));
    }

    function rewrite_rule() {
        add_rewrite_tag( '%letterpress-newsletter%', '([^&]+)' );
        add_rewrite_rule( '^letterpress-newsletter/([^/]*)/?', 'index.php?letterpress-newsletter=$matches[1]','top' );

        add_rewrite_tag( '%letterpress-unsubscribe%', '([^&]+)' );
        add_rewrite_rule( '^letterpress-unsubscribe/([^/]*)/?', 'index.php?letterpress-unsubscribe=$matches[1]','top' );

        //letterpress-unsubscribe
    }

    function register_query_vars( $vars ) {
        $vars[] = 'letterpress-newsletter';
        $vars[] = 'letterpress-unsubscribe';
        return $vars;
    }

    function template_include_view_campaign( $template ) {
	    global $wp_query, $wpdb;

	    if ( ! isset( $wp_query->query_vars['letterpress-newsletter'] ) || is_admin() ) {
		    return $template;
	    }

	    $email_token = sanitize_text_field( $wp_query->query_vars['letterpress-newsletter'] );

	    $campaign = $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}lp_campaigns WHERE slug = %s ", $email_token ) );
	    if ( empty( $campaign ) ) {
		    return $template;
	    }
	    echo lp_decode_text( $campaign->message );

	    die();
    }

    function template_include_unsubscribe( $template ) {
        global $wp_query, $wpdb;

        if ( ! isset($wp_query->query_vars['letterpress-unsubscribe']) || is_admin() ){
            return $template;
        }

        $user_token = sanitize_text_field($wp_query->query_vars['letterpress-unsubscribe']);

        $user = $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}lp_subscribers WHERE token = %s ", $user_token ) );
        if (empty($user)){
            //return $template;
        }

        $wpdb->update(
            "{$wpdb->prefix}lp_subscribers",
            array('status' => 'U', 'unsub_time' => time()),
            array('id' => $user->id)
        );

        die(__('Unsubscribe successfully, you will no more receive newsletter from us'));
    }

    /**
     * Auto flush rewrite rules
     */
    public function flush_rewrite_rules(){
        $is_required_flush = get_option('lp_required_flush_rewrite_rules');
        if ($is_required_flush){
            flush_rewrite_rules();
            delete_option('lp_required_flush_rewrite_rules');
        }
    }
    
    
}