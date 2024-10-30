<?php
namespace letterpress;

class Helper{

    /**
     * @return bool|int
     *
     * Is required sync subscriber with wp users
     */
    public function required_sync(){
        global $wpdb;

        $lastSyncIDQuery = (int) $wpdb->get_var(" select MAX(wp_user_id) as lastSyncWPUserID from {$wpdb->prefix}lp_subscribers ");
        $required_sync = (int) $wpdb->get_var("select COUNT(ID) from {$wpdb->prefix}users WHERE ID > {$lastSyncIDQuery} ");

        return $required_sync;
    }

    /**
     * @param bool $status
     * @return int
     *
     * @since $total subscriber count
     */

    public function total_subscribers($status = false){
        global $wpdb; //This is used only if making any database queries

        $query = "select COUNT(id) from {$wpdb->prefix}lp_subscribers ";
        if ($status){
            $query .= $wpdb->prepare( " WHERE status = %s ", $status ) ;
        }
        $total_items = (int) $wpdb->get_var($query);
        return $total_items;
    }

    /**
     * @return int
     *
     * Total Users
     */

    public function total_users(){
        global $wpdb; //This is used only if making any database queries

        $total_users = (int) $wpdb->get_var("select COUNT(ID) from {$wpdb->users}");
        return $total_users;
    }

    /**
     * @return int
     *
     * Total Campaigns
     */
    public function total_campaigns(){
        global $wpdb; //This is used only if making any database queries

        $total_items = (int) $wpdb->get_var("select COUNT(id) from {$wpdb->prefix}lp_campaigns");
        return $total_items;
    }

    /**
     * @return int
     *
     * Get the total Sent Campaigns
     */
    public function sent_campaigns(){
        global $wpdb; //This is used only if making any database queries

        $total_items = (int) $wpdb->get_var("select COUNT(id) from {$wpdb->prefix}lp_campaigns WHERE status = 'sent' ");
        return $total_items;
    }

    /**
     * @return int
     *
     * Get the total sending campaigns
     */
    public function sending_campaigns(){
        global $wpdb; //This is used only if making any database queries

        $total_items = (int) $wpdb->get_var("select COUNT(id) from {$wpdb->prefix}lp_campaigns WHERE status = 'sending' ");
        return $total_items;
    }

    /**
     * @return int
     *
     * Get the total E-Mail Sent to all Campaign
     */
    public function total_email_sent(){
        global $wpdb; //This is used only if making any database queries

        $total_items = (int) $wpdb->get_var("select SUM(total) from {$wpdb->prefix}lp_campaigns");
        return $total_items;
    }

    /**
     * @param null $option
     * @return bool|mixed
     *
     * Retrieve LetterPress Option
     */
    public function get_option($option = null, $default = false) {
	    $options = (array) maybe_unserialize( get_option( 'lp_option' ) );

	    if ( ! $option ) {
		    return $options;
	    }

	    if ( array_key_exists( $option, $options ) ) {
		    return $options[ $option ];
	    }

	    return $default;
    }


    /**
     * @param int $length
     * @param string $keyspace
     * @return string
     *
     * Return random string
     */
    public function random_str($length = 64, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        $pieces = array();
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * @return string
     *
     * Subscriber Unique Token
     */
    public function subscriber_unique_token(){
        global $wpdb;

        do{
            $rand_string = $this->random_str();
            $is_duplicate = (int) $wpdb->get_var( $wpdb->prepare( "select count(id) from {$wpdb->prefix}lp_subscribers WHERE token = %s ", $rand_string ) );
        } while ($is_duplicate);

        return $rand_string;
    }

    /**
     * @return string
     *
     * Get the unique slug for campaigns
     */
    public function campaign_unique_slug(){
        global $wpdb;

        do{
            $rand_string = $this->random_str();
            $is_duplicate = (int) $wpdb->get_var( $wpdb->prepare( "select count(id) from {$wpdb->prefix}lp_campaigns WHERE slug = %s ", $rand_string ) );
        } while ($is_duplicate);

        return $rand_string;
    }

    public function campaign_url($campaign_slug){
        $url = esc_url(untrailingslashit(get_site_url()).'/letterpress-newsletter/'.$campaign_slug);
        return $url;
    }

    public function unsubscribe_url($token){
        $url = esc_url(untrailingslashit(get_site_url()).'/letterpress-unsubscribe/'.$token);
        return $url;
    }


    /**
     * @return bool|mixed
     *
     * Campaigns E-Mail sents from Email
     */
    public function from_email(){
        $from_email = $this->get_option('from_email');
        if ( ! $from_email){
            $from_email = get_option('admin_email');
        }
        return $from_email;
    }

    /**
     * @return bool|mixed
     *
     * Campaigns E-Mail sents from Name
     */
    public function from_name(){
        $from_name = $this->get_option('from_name');
        if ( ! $from_name){
            $from_name = get_option('blogname');
        }
        return $from_name;
    }

    public function subscriber_status_context($status = "S"){


    }
    
}