<h3><?php _e('Campaign List', 'letterpress'); ?></h3>

<a class="button-primary" href="<?php echo add_query_arg(array('sub_page' => 'add_campaign')); ?>"><?php _e('Add Campaign', 'letterpress'); ?></a>

<?php
global $wpdb;
$campaigns = $wpdb->get_results("select * from {$wpdb->prefix}lp_campaigns ORDER BY id DESC;" );

if ( is_array( $campaigns ) && count( $campaigns ) ) {
?>

<div class="notice notice-info">
    <p>
        <?php _e('If you running multiple campaigns at a time, another campaign will start after complete running one, the last running campaign will finish first.', 'letterpress'); ?>
    </p>
</div>

<table class="links-table">
    <tr>
        <td><?php _e('Name', 'letterpress'); ?></td>
        <td><?php _e('Subject', 'letterpress'); ?></td>
        <td><?php _e('Status', 'letterpress'); ?></td>
        <td><?php _e('Progress', 'letterpress'); ?></td>
        <td><?php _e('Actions', 'letterpress'); ?></td>
    </tr>

    <?php
    foreach ($campaigns as $campaign){
        ?>
        <tr>
            <td><?php echo stripslashes($campaign->name); ?></td>
            <td><?php echo stripslashes($campaign->subject); ?></td>
            <td>
                <?php
                switch ($campaign->status){
                    case 'new':
                        echo "<span><i class='dashicons dashicons-controls-play'></i> {$campaign->status}</span>";
                        break;
                    case 'sending':
                        echo "<div class='updating-message'><p>{$campaign->status}</p></div>";
                        break;
                    case 'sent':
                        echo "<p><i class='dashicons dashicons-yes'></i> {$campaign->status}</p>";
                        break;
                    case 'paused':
                        echo "<span><i class='dashicons dashicons-controls-pause'></i> {$campaign->status}</span>";
                        break;
                }
                ?>
            </td>

            <td>
                <?php echo  $campaign->total; ?> / <?php echo LPRESS()->h->total_subscribers("S"); ?>
            </td>

            <td>
                <a href="<?php echo LPRESS()->h->campaign_url($campaign->slug); ?>" class="button" target="_blank"> <i class="dashicons dashicons-visibility"></i> <?php _e('View', 'letterpress'); ?></a>
                <a href="<?php echo add_query_arg(array('sub_page' => 'edit_campaign', 'campaign_id' => $campaign->id)); ?>" class="button"> <i class="dashicons dashicons-edit"></i> <?php _e('Edit', 'letterpress'); ?></a>

                <?php
                if ($campaign->status !== 'sending'){
                    echo "<a class='button' href='".admin_url('admin.php?action=lp_campaign_control&status=sending&campaign_id='.$campaign->id)."'> <i class='dashicons dashicons-controls-play'></i> ".__('Start', 'letterpress')." </a>";
                }else{
                    echo "<a class='button' href='".admin_url('admin.php?action=lp_campaign_control&status=paused&campaign_id='.$campaign->id)."'> <i class='dashicons dashicons-controls-pause'></i> ".__('Stop', 'letterpress')." </a>";
                }

                ?>

                <a href="<?php echo admin_url('admin.php?action=lp_test_campaign&campaign_id='.$campaign->id); ?>" class="button button-primary"> <i class="dashicons dashicons-welcome-widgets-menus"></i> <?php _e('Test', 'letterpress'); ?></a>


                <a href="<?php echo admin_url('admin.php?action=lp_campaign_control&status=delete&campaign_id='.$campaign->id); ?>" class="button button-link-delete"> <i class="dashicons dashicons-trash"></i> <?php _e('Delete', 'letterpress'); ?></a>
            </td>

        </tr>
        <?php
    }
    ?>

</table>
<?php } else {
    ?>
    <div class="notice notice-info" style="margin-top: 50px;">
        <p>
            <?php _e('You have not created any campaign yet, create your first campaign now.', 'letterpress'); ?>
        </p>
    </div>
    <?php
}