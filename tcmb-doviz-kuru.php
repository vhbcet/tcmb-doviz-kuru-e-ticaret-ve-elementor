<?php
/**
 * Plugin Name: TCMB Döviz Kuru – E-Ticaret ve Elementor
 * Plugin URI:  https://github.com/vhbcet/tcmb-doviz-kuru-e-ticaret-ve-elementor
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
define( 'TCMB_DOVIZ_KURU_BASENAME', plugin_basename( __FILE__ ) );
define( 'TCMB_DOVIZ_KURU_PATH', plugin_dir_path( __FILE__ ) );

require_once TCMB_DOVIZ_KURU_PATH . 'includes/core.php';
require_once TCMB_DOVIZ_KURU_PATH . 'includes/frontend.php';
require_once TCMB_DOVIZ_KURU_PATH . 'includes/backend.php';

register_activation_hook( __FILE__, 'tcmb_doviz_kuru_activate' );
