<?php
namespace letterpress;

class Installation{
    public function __construct(){
        register_activation_hook(LPRESS_FILE, array($this, 'activation'));

        register_deactivation_hook(LPRESS_FILE, array($this, 'lp_deactivation'));
    }


    public function activation(){
        if ( ! get_option('LPRESS_VERSION')) {
            update_option('LPRESS_VERSION', LPRESS_VERSION);

            //Creating table
            global $wpdb;

            //sex='',M,F
            //status=S,U

            $charset_collate = $wpdb->get_charset_collate();

            $subscribers_table_name = $wpdb->prefix . 'lp_subscribers';
            $campaigns_table_name = $wpdb->prefix . 'lp_campaigns';

            $subscribers_sql = "CREATE TABLE {$subscribers_table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL DEFAULT '',
            email varchar(100) NOT NULL DEFAULT '',
            token varchar(191) NOT NULL DEFAULT '',
            status varchar(1) NOT NULL DEFAULT 'S',
            updated int(11) NOT NULL DEFAULT '0',
            last_activity int(11) NOT NULL DEFAULT '0',
            sex char(1) NOT NULL DEFAULT '',
            ip varchar(50) NOT NULL DEFAULT '',
            wp_user_id int(11) NOT NULL DEFAULT '0',
            unsub_time int(11) NOT NULL DEFAULT '0',
            created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id) ) $charset_collate;";

            $campaigns_sql = "CREATE TABLE {$campaigns_table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL DEFAULT '',
            subject varchar(255) NOT NULL DEFAULT '',
            message longtext NOT NULL DEFAULT '',
            slug varchar(255) NOT NULL DEFAULT '',
            status enum('new','sending','sent','paused') NOT NULL DEFAULT 'new',
            total int(11) NOT NULL DEFAULT '0',
            last_subscriber_id int(11) NOT NULL DEFAULT '0',
            theme varchar(50) NOT NULL DEFAULT '',
            click_count int(11) NOT NULL DEFAULT '0',
            open_count int(11) NOT NULL DEFAULT '0',
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id) ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($subscribers_sql);
            dbDelta($campaigns_sql);
        }

        /**
         * Set LP Cron schedule for sending Campaigns
         */
        if (! wp_next_scheduled ( 'lp_cron_interval' )) {
            wp_schedule_event(time(), 'lp_cron_interval_time', 'lp_cron_interval');
        }

        /**
         * Set Rules for lp required flush
         */
        update_option('lp_required_flush_rewrite_rules', time());
    }

    /**
     * Deactivation task
     */

    public function lp_deactivation() {
        wp_clear_scheduled_hook('lp_cron_interval');
    }

}