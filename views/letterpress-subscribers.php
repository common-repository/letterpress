<div class="wrap">

    <h2><?php _e('Subscribers', 'letterpress'); ?></h2>

    <?php
    $required_sync = LPRESS()->h->required_sync();

    if ($required_sync){
        ?>
        <a class="button-primary" href="<?php echo admin_url('admin.php?action=lp_sync_users') ?>">
            <?php _e('Sync with Users', 'letterpress'); ?> (<?php echo esc_html( $required_sync ); ?>)
        </a>
    <?php } ?>

    <?php
    $subscribers_list = new \letterpress\Subscribers_List();
    $subscribers_list->prepare_items();
    ?>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="lp-subscribers-filter" method="post">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
        <!-- Now we can render the completed list table -->
        <?php
        $subscribers_list->search_box(__('Search Subscriber'), 'search-subscriber' );
        $subscribers_list->display() ?>
    </form>

</div>