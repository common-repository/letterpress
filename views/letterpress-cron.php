<div class="wrap">
    <h1><?php _e("Cron Settings", 'letterpress'); ?></h1>

    <div class="lp-notice-box">
        <p>
            First, you need to disable the script to be executed every time someone loads one of your pages. To do this, open the <b>wp-config.php</b> file located at <b><?php echo ABSPATH; ?></b> in your main WordPress folder and add the following line before the  <b>"/* That's all, stop editing! Happy blogging. */"</b>  line:
        </p>

        <p>
            <code>define('DISABLE_WP_CRON', true);</code>
        </p>


        <p><strong>Then follow one of the below method</strong></p>

        <h3>Method 1 (cPanel)</h3>

        <p>
            Login to your cPanel and go to the <strong>Cron jobs</strong> tool located in the <strong>Advanced</strong> section. <br />
            Place below code to command field
        </p>
        <code>wget -q -O - <?php echo site_url(); ?>/wp-cron.php?doing_wp_cron >/dev/null 2>&1</code>

        <p>
            The Cron jobs tool has some of the most common schedules preset, so you can just select Every 15 minutes from the minutes drop-down and place a "*" symbol in the others.
        </p>

        <img src="<?php echo LPRESS()->url.'assets/images/cronjob.jpg'; ?>" />




        <h3>Method 2 (cPanel)</h3>

        <p>
            Follow <strong>Method 1</strong> and just change the command code to below
        </p>

        <?php
        $cron_path = ABSPATH.'wp-cron.php';
        ?>

        <code>/usr/local/bin/php -q <?php echo $cron_path; ?> >/dev/null 2>&1</code>


        <h3>Method 3 (VPS / SSH)</h3>

        <p>
            1. Log in to your account using SSH. <br />
            2. At the command prompt, type the following command: <code>crontab -e</code> <br />
            3. Type <b>o</b> to enter editing mode and start a new line of text. <br />
            <code>*/30 * * * *  /usr/local/bin/php -q <?php echo $cron_path; ?></code> or <br />
            <code>*/30 * * * *  php -q <?php echo $cron_path; ?></code> or <br />
            4. Press Esc, type :wq and then press Enter. The new cron job settings take effect immediately. <br />

            Note: Above command for 30 minutes interval cron
        </p>



        <h4>Optional cron interval helper commands, choose any one</h4>

        <p>
            <strong>For every minute</strong> <br />
            <code>* * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>

        <p>
            <strong>For every 5 minutes</strong> <br />
            <code>*/5 * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>

        <p>
            <strong>For every 10 minutes</strong> <br />
            <code>*/10 * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>

        <p>
            <strong>For every 20 minutes</strong> <br />
            <code>*/20 * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>

        <p>
            <strong>For every 30 minutes</strong> <br />
            <code>*/30 * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>

        <p>
            <strong>For every 40 minutes</strong> <br />
            <code>*/40 * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>
        <p>
            <strong>For every 50 minutes</strong> <br />
            <code>*/50 * * * *  php -q <?php echo $cron_path; ?></code> <br />
        </p>

        <p>
            <strong>For every hour</strong> <br />
            <code>0 * * * * php -q <?php echo $cron_path; ?></code> <br />
        </p>
    </div>



</div>
