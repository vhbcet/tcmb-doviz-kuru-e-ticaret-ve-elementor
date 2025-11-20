<?php
/**
 * Plugin Name: TCMB Döviz Kuru – E-Ticaret ve Elementor
 * Description: TCMB today.xml verisini kullanarak USD, EUR, GBP, JPY, CNY ve AED kurlarını çeker. Kısa kodlar, WooCommerce ve Elementor entegrasyonu ile e-ticaret sitelerinde dinamik kur kullanmanıza yardımcı olur.
 * Version:     2.3.1
 * Author:      Hedef Hosting
 * Author URI:  https://hedefhosting.com.tr
 * Text Domain: tcmb-doviz-kuru-e-ticaret-ve-elementor
 * Domain Path: /languages
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

define( 'TCMB_DOVIZ_KURU_VERSION', '2.3.1' );
define( 'TCMB_DOVIZ_KURU_TEXTDOMAIN', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' );
define( 'TCMB_DOVIZ_KURU_OPTION_GENERAL', 'tcmb_doviz_kuru_general_options' );
define( 'TCMB_DOVIZ_KURU_OPTION_WC', 'tcmb_doviz_kuru_wc_options' );
define( 'TCMB_DOVIZ_KURU_PLUGIN_FILE', __FILE__ );

tcmb_doviz_kuru_require_files();

register_activation_hook( __FILE__, 'tcmb_doviz_kuru_activate' );

/**
 * Load plugin files.
 */
function tcmb_doviz_kuru_require_files() {
        $base_path = plugin_dir_path( __FILE__ ) . 'includes/';

        require_once $base_path . 'general.php';
        require_once $base_path . 'rates.php';
        require_once $base_path . 'shortcodes.php';
        require_once $base_path . 'woocommerce.php';
        require_once $base_path . 'admin.php';
        require_once $base_path . 'elementor.php';
}
