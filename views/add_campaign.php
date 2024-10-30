<div class="wrap">

    <h1><?php _e('Add Campaign', 'letterpress'); ?></h1>

    <form action="<?php echo admin_url('admin.php'); ?>" method="post">
        <input type="hidden" name="action" value="lp_save_campaign" />
        <?php wp_nonce_field('letterpress_add_campaign') ?>

        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label><?php _e('Campaign Name', 'letterpress'); ?></label>
            </div>
            <div class="lp-settings-field">
                <input type="text" name="name" class="regular-text" required="required">
                <p class="desc">
                    <?php _e('Name of the campaign', 'letterpress'); ?>
                </p>
            </div>
        </div>


        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label><?php _e('Campaign Subject', 'letterpress'); ?> </label>
            </div>
            <div class="lp-settings-field">
                <input type="text" name="subject" class="regular-text" required="required">
                <p class="desc">
                    <?php _e('Subject of the campaign that will sent to campaign E-Mail subject', 'letterpress'); ?>
                </p>
            </div>
        </div>

        <div class="lp-settings-row">
            <div class="lp-settings-label">
                <label><?php _e('HTML Campaign Message (Body)', 'letterpress'); ?></label>
            </div>
            <div class="lp-settings-field">
                <textarea class="wp-editor-area" name="message" required="required" rows="15" ></textarea>

                <p class="desc">
                    <?php _e('Main Campaign Body in HTML format', 'letterpress'); ?>
                </p>
            </div>
        </div>

        <p>
            <input type="submit" class="button-primary" name="letterpress_save_campaign" value="<?php _e('Save Campaign', 'letterpress'); ?>">
        </p>
    </form>

</div>