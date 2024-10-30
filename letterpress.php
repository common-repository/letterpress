<?php
/*
 * Plugin Name:       LetterPress - Newsletter Plugin
 * Description:       A newsletter plugin for WordPress
 * Version:           1.2.2
 * Author:            Themeqx
 * Author URI:        https://www.themeqx.com
 * Text Domain:       letterpress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define('LPRESS_FILE', __FILE__);
define('LPRESS_PATH', plugin_dir_path(LPRESS_FILE));
define('LPRESS_VERSION', '1.2.2');

require LPRESS_PATH . 'functions.php';


if (version_compare(PHP_VERSION, '5.4.0', '>')) {
    require LPRESS_PATH . 'classes/Init.php';
    function LPRESS(){
        return new \letterpress\Init();
    }
    LPRESS()->run();
}else{
    add_action('admin_notices', 'lp_show_older_php_notice');
}
if ( ! function_exists('lp_show_older_php_notice')){
    function lp_show_older_php_notice(){
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'You are using PHP version <code>'.PHP_VERSION.'</code> and you need upgrade PHP to minimum <code>5.4.0</code> in order to use LetterPress', 'letterpress' ); ?></p>
        </div>
<?php
    }
}