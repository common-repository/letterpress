<div class="wrap">
    <h1><?php _e('Settings', 'letterpress'); ?></h1>

    <form method="post" action="options.php">

        <?php
        settings_fields( 'lp_option' );
        ?>

        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label><?php _e('Test Campaign E-Mail', 'letterpress'); ?></label>
            </div>
            <div class="lp-settings-field">
                <input name="lp_option[test_campaign_email]" type="email" value="<?php echo LPRESS()->h->get_option('test_campaign_email', get_option('admin_email')); ?>" spellcheck="false" placeholder="<?php echo get_option('admin_email'); ?>">

                <p class="desc">
                    <?php _e('Test campaign will be sent to this E-Mail address', 'letterpress'); ?>
                </p>
            </div>
        </div>

        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label><?php _e('From E-Mail', 'letterpress'); ?></label>
            </div>
            <div class="lp-settings-field">
                <input name="lp_option[from_email]" type="email" value="<?php echo LPRESS()->h->from_email(); ?>" spellcheck="false">

                <p class="desc">
                    <?php _e('The email address which emails are sent from.', 'letterpress'); ?>
                </p>
            </div>
        </div>

        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label><?php _e('From Name', 'letterpress'); ?></label>
            </div>
            <div class="lp-settings-field">

                <input name="lp_option[from_name]" type="text" value="<?php echo LPRESS()->h->from_name(); ?>" spellcheck="false">

                <p class="desc">
                    <?php _e('The name which emails are sent from.', 'letterpress'); ?>
                </p>
            </div>
        </div>


        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label for="interval_minutes"><?php _e('Interval Minutes', 'letterpress'); ?></label>
            </div>
            <div class="lp-settings-field">

                <input name="lp_option[interval_minutes]" type="text" id="interval_minutes" value="<?php echo LPRESS()->h->get_option('interval_minutes', 5); ?>" class="regular-text" />
                <p class="description"> <?php _e('Interval in minutes, send email after certain time, split the time with interval to send properly', 'letterpress'); ?> </p>
                <p class="description"> <?php _e('Note: If you using shared server, set <code>15</code> min interval, because most shared server not support less then 15 minutes interval', 'letterpress'); ?> </p>
            </div>
        </div>


        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label for="number_of_email_per_interval"><?php _e('Number of E-Mail per interval'); ?></label>
            </div>
            <div class="lp-settings-field">
                <input name="lp_option[number_of_email_per_interval]" type="text" id="number_of_email_per_interval" value="<?php echo LPRESS()->h->get_option('number_of_email_per_interval', 1); ?>" class="regular-text" />
                <p class="description"><?php _e('Send number of E-Mail per interval, set the number based on your server performance, min 1 per 60 minute Interval to maximum unlimited', 'letterpress'); ?></p>
            </div>
        </div>


        <?php submit_button(); ?>

    </form>

</div>