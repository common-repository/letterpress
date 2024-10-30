<div class="wrap">
    <h1><?php _e("LetterPress Dashboard", 'letterpress'); ?></h1>

    <div class="lp-notice-box">
        <p>
            <?php _e(sprintf('LetterPress using default %s function to send the campaign, make sure your default WordPress E-Mail is working to send any campaign to your subscribers.', '<code>wp_mail()</code>'),'letterpress'); ?>
        </p>
    </div>


    <div class="stats-box bg-primary">
        <div class="text-value"><?php echo LPRESS()->h->total_subscribers(); ?></div>
        <div><?php _e('Subscribers', 'letterpress'); ?></div>
    </div>

    <div class="stats-box bg-success">
        <div class="text-value"><?php echo LPRESS()->h->total_subscribers("S"); ?></div>
        <div><?php _e('Active Subscribers', 'letterpress'); ?></div>
    </div>

    <div class="stats-box bg-secondary">
        <div class="text-value"><?php echo LPRESS()->h->total_users()   ; ?></div>
        <div><?php _e('Users', 'letterpress'); ?></div>
    </div>


    <div class="stats-box bg-info">
        <div class="text-value"><?php echo LPRESS()->h->total_campaigns()   ; ?></div>
        <div><?php _e('Campaigns', 'letterpress'); ?></div>
    </div>


    <div class="stats-box bg-warning">
        <div class="text-value"><?php echo LPRESS()->h->total_email_sent()   ; ?></div>
        <div><?php _e('E-Mail Sent (All Campaigns)', 'letterpress'); ?></div>
    </div>



    <div class="stats-box bg-success">
        <div class="text-value">
            <?php
            $interval_minutes = LPRESS()->h->get_option('interval_minutes');
            $number_of_email_per_interval = LPRESS()->h->get_option('number_of_email_per_interval');

            if ($interval_minutes > 0 && $number_of_email_per_interval > 0){
                echo round((60 / $interval_minutes) * $number_of_email_per_interval, 1);
            }else{
                echo 0;
            }
            ?>
        </div>
        <div><?php _e('E-Mail sending per hour', 'letterpress'); ?></div>
    </div>

    <div class="stats-box bg-danger">
        <div class="text-value"><?php echo LPRESS()->h->total_subscribers("U"); ?></div>
        <div><?php _e('Un-Subscribers', 'letterpress'); ?></div>
    </div>
    <div class="stats-box bg-danger">
        <div class="text-value"><?php echo LPRESS()->h->total_subscribers("B"); ?></div>
        <div><?php _e('Block Subscribers', 'letterpress'); ?></div>
    </div>


    <div class="stats-box bg-dark">
        <div class="text-value"><?php echo LPRESS()->h->sent_campaigns(); ?> </div>
        <div><?php _e('Completed Campaigns', 'letterpress'); ?></div>
    </div>


    <div class="stats-box bg-white">
        <div class="text-value"><?php echo LPRESS()->h->sending_campaigns(); ?> </div>
        <div><?php _e('Sending Campaigns', 'letterpress'); ?></div>
    </div>


    <?php
    $last_check = get_option('lp_last_checked_time');
    if ($last_check){
        ?>
        <div class="stats-box bg-primary">
            <div class="text-value"><?php _e('Last checked', 'letterpress'); ?></div>
            <div><?php echo $last_check; ?></div>
        </div>
    <?php
    }
    ?>

</div>