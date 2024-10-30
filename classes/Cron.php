<?php
namespace letterpress;
if ( ! defined( 'ABSPATH' ) ) exit;

class Cron{

    public function __construct(){
        add_filter('cron_schedules', array($this, 'lp_cron_add_interval'));
        add_action('lp_cron_interval', array($this, 'send_lp_campaign'));

        //TEMP, only for testing campaign sending cron
        //add_action('admin_init', array($this, 'send_lp_campaign'));
    }

    /**
     * @param $schedules
     * @return mixed
     *
     * Add LetterPress interval to send the E-Mail
     */
    function lp_cron_add_interval($schedules) {
        $intervalMin = (int) LPRESS()->h->get_option('interval_minutes', 5);
        $intervalSec = $intervalMin * 60;

        $schedules['lp_cron_interval_time']=array(
            'interval'  => $intervalSec,
            'display'   => __('Letter Press Interval', 'letterpress')
        );

        return $schedules;
    }

    /**
     * Send the campaign Right Now
     */
    function send_lp_campaign() {
        global $wpdb;

        $last_checked = date_i18n( get_option('date_format') ).' - '.date_i18n(get_option('time_format'));
        update_option('lp_last_checked_time', $last_checked);

        $number_of_email = LPRESS()->h->get_option('number_of_email_per_interval');
        $campaign = $wpdb->get_row("select * from {$wpdb->prefix}lp_campaigns WHERE status = 'sending' ORDER BY id ASC LIMIT 1 ");

        if ( empty($campaign) ){
            return;
        }

        $get_subscribers = $wpdb->get_results("select * from {$wpdb->prefix}lp_subscribers WHERE id > {$campaign->last_subscriber_id} AND status = 'S' ORDER BY id ASC LIMIT {$number_of_email} ");

        if (empty($get_subscribers)){
            //If no subscribers, mark this campaign to completed
            $wpdb->update(
                "{$wpdb->prefix}lp_campaigns",
                array('status' => 'sent'),
                array('id' => $campaign->id)
            );
            return;
        }

        $total_sent = $campaign->total;

        foreach ($get_subscribers as $subscriber){
            //Send Email Now

            $message = apply_filters('lp_email_message', $this->get_the_campaign_message($campaign));
            $email_header = apply_filters('lp_email_header', $this->get_email_header($subscriber, $campaign));
            $email_footer = apply_filters('lp_email_footer', $this->get_email_footer($subscriber));

            $message = $email_header.$message.$email_footer;

            $fromEmail = LPRESS()->h->from_email();
            $fromName = LPRESS()->h->from_name();
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$fromName} <{$fromEmail}>");

            //Send Now
            $isSent = false;
            $isSent = wp_mail($subscriber->email, stripslashes($campaign->subject), $message, $headers );

            if ($isSent){
                $total_sent = $total_sent + 1;

                //Track the progress of sending campaign
                $wpdb->update(
                    "{$wpdb->prefix}lp_campaigns",
                    array(
                        'total' => $total_sent,
                        'last_subscriber_id'    => $subscriber->id
                    ),
                    array('id' => $campaign->id)
                );
            }

        }
    }

    public function get_the_campaign_message( $campaign ) {
	    return lp_decode_text( $campaign->message );
    }

    /**
     * @param null $subscriber
     * @param null $campaign
     * @return mixed|string
     *
     * Get the E-Mail Body Header
     */
    public function get_email_header($subscriber = null, $campaign = null){
        ob_start();
        include LPRESS_PATH.'template/email-header.php';
        $header = ob_get_clean();

        if ($campaign){
            $campaign_view_url = LPRESS()->h->campaign_url($campaign->slug);

            $find = array('{view_campaign_url}');
            $replace = array($campaign_view_url);

            $header = str_replace($find, $replace, $header);
        }
        return $header;
    }

    /**
     * @param null $subscriber
     * @param null $campaign
     * @return mixed|string
     *
     * Get the E-Mail body Footer
     */
    public function get_email_footer($subscriber = null, $campaign = null){
        ob_start();
        include LPRESS_PATH.'template/email-footer.php';
        $footer = ob_get_clean();

        if ($subscriber){
            $unsub_url = LPRESS()->h->unsubscribe_url($subscriber->token);

            $find = array('{unsubscribe_url}');
            $replace = array($unsub_url);

            $footer = str_replace($find, $replace, $footer);
        }
        return $footer;
    }

}


