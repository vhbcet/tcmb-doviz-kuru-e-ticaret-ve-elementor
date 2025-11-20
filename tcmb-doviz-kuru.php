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

/**
 * Load plugin textdomain.
 */
function tcmb_doviz_kuru_load_textdomain() {
	load_plugin_textdomain(
		TCMB_DOVIZ_KURU_TEXTDOMAIN,
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'tcmb_doviz_kuru_load_textdomain' );

/**
 * Activation: set default options.
 */
function tcmb_doviz_kuru_activate() {
	$general_defaults = array(
		'field'          => 'ForexSelling',
		'decimals'       => 2,
		'show_symbol'    => 1,
		'show_flag'      => 0,
		'show_date'      => 1,
		'cache_minutes'  => 60,
		'error_message'  => __( 'Şu anda döviz kuru alınamıyor. Lütfen daha sonra tekrar deneyin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
	);

	$wc_defaults = array(
		'enabled'             => 0,
		'mode'                => 'single', // single|per_product
		'input_currency'      => 'USD',
		'store_currency'      => 'TRY',
		'show_original_price' => 1,
	);

	if ( ! get_option( TCMB_DOVIZ_KURU_OPTION_GENERAL ) ) {
		update_option( TCMB_DOVIZ_KURU_OPTION_GENERAL, $general_defaults );
	}

	if ( ! get_option( TCMB_DOVIZ_KURU_OPTION_WC ) ) {
		update_option( TCMB_DOVIZ_KURU_OPTION_WC, $wc_defaults );
	}
}
register_activation_hook( __FILE__, 'tcmb_doviz_kuru_activate' );

/**
 * Get general options.
 */
function tcmb_doviz_kuru_get_general_options() {
	$defaults = array(
		'field'          => 'ForexSelling',
		'decimals'       => 2,
		'show_symbol'    => 1,
		'show_flag'      => 0,
		'show_date'      => 1,
		'cache_minutes'  => 60,
		'error_message'  => __( 'Şu anda döviz kuru alınamıyor. Lütfen daha sonra tekrar deneyin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
	);

	$options = get_option( TCMB_DOVIZ_KURU_OPTION_GENERAL, array() );

	return wp_parse_args( $options, $defaults );
}

/**
 * Get WooCommerce options.
 */
function tcmb_doviz_kuru_get_wc_options() {
	$defaults = array(
		'enabled'             => 0,
		'mode'                => 'single', // single|per_product
		'input_currency'      => 'USD',
		'store_currency'      => 'TRY',
		'show_original_price' => 1,
	);

	$options = get_option( TCMB_DOVIZ_KURU_OPTION_WC, array() );

	return wp_parse_args( $options, $defaults );
}

/**
 * Fetch and cache TCMB rates.
 *
 * Returns array like:
 * [
 *   'USD' => ['ForexSelling' => 32.50, 'ForexBuying' => 32.10, 'Unit' => 1],
 *   'EUR' => [...],
 *   '_DATE' => 'YYYY-MM-DD'
 * ]
 */
function tcmb_doviz_kuru_get_rates() {
	$general = tcmb_doviz_kuru_get_general_options();
	$cache_key = 'tcmb_doviz_kuru_cache';
	$cached    = get_transient( $cache_key );

	if ( ! empty( $cached ) && is_array( $cached ) ) {
		return $cached;
	}

        $response = wp_safe_remote_get(
                'https://www.tcmb.gov.tr/kurlar/today.xml',
                array(
                        'timeout'    => 10,
                        'user-agent' => 'tcmb-doviz-kuru/' . TCMB_DOVIZ_KURU_VERSION,
                )
        );

        if ( is_wp_error( $response ) ) {
                return array();
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $status_code ) {
                return array();
        }

        $body = wp_remote_retrieve_body( $response );

        if ( empty( $body ) ) {
                return array();
        }

        $previous_internal_errors = libxml_use_internal_errors( true );
        $xml                      = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous_internal_errors );

        if ( ! $xml ) {
                return array();
        }

	$rates = array();

	$date = '';
	if ( isset( $xml['Date'] ) ) {
		$date = (string) $xml['Date'];
	} elseif ( isset( $xml['Tarih'] ) ) {
		$date = (string) $xml['Tarih'];
	} else {
		$date = current_time( 'Y-m-d' );
	}

	foreach ( $xml->Currency as $currency ) {
		$code = (string) $currency['CurrencyCode'];

		if ( ! $code ) {
			continue;
		}

		$unit = isset( $currency->Unit ) ? (int) $currency->Unit : 1;
		if ( $unit <= 0 ) {
			$unit = 1;
		}

		$fields = array( 'ForexBuying', 'ForexSelling', 'BanknoteBuying', 'BanknoteSelling' );
		$data   = array( 'Unit' => $unit );

		foreach ( $fields as $field ) {
			if ( isset( $currency->{$field} ) ) {
				$value = (string) $currency->{$field};
				$value = str_replace( ',', '.', $value );
				$float = (float) $value;

				// Normalize by unit (JPY gibi 100 birim üzerinden gelenler için).
				if ( $unit > 1 ) {
					$float = $float / $unit;
				}
				$data[ $field ] = $float;
			}
		}

		$rates[ $code ] = $data;
	}

	$rates['_DATE'] = $date;

	$cache_minutes = max( 1, (int) $general['cache_minutes'] );
	set_transient( $cache_key, $rates, $cache_minutes * MINUTE_IN_SECONDS );

	return $rates;
}

/**
 * Get single rate value.
 *
 * @param string $code  Currency code (USD, EUR, GBP, JPY, CNY, AED).
 * @param string $field TCMB field (ForexSelling, ForexBuying, BanknoteSelling, BanknoteBuying).
 *
 * @return float|null
 */
function tcmb_doviz_kuru_get_rate_value( $code, $field ) {
	$rates = tcmb_doviz_kuru_get_rates();
	if ( empty( $rates ) || empty( $rates[ $code ] ) ) {
		return null;
	}

	if ( isset( $rates[ $code ][ $field ] ) ) {
		return (float) $rates[ $code ][ $field ];
	}

	// Fallback: ForexSelling.
	if ( isset( $rates[ $code ]['ForexSelling'] ) ) {
		return (float) $rates[ $code ]['ForexSelling'];
	}

	return null;
}

/**
 * Helper: symbol and flag maps.
 */
function tcmb_doviz_kuru_get_symbols() {
	return array(
		'USD' => '$',
		'EUR' => '€',
		'GBP' => '£',
		'JPY' => '¥',
		'CNY' => '¥',
		'AED' => 'د.إ',
		'TRY' => '₺',
	);
}

function tcmb_doviz_kuru_get_flags() {
	return array(
		'USD' => '🇺🇸',
		'EUR' => '🇪🇺',
		'GBP' => '🇬🇧',
		'JPY' => '🇯🇵',
		'CNY' => '🇨🇳',
		'AED' => '🇦🇪',
		'TRY' => '🇹🇷',
	);
}

/**
 * Render single rate HTML.
 *
 * @param string $code Currency code.
 * @param array  $atts Shortcode attributes.
 *
 * @return string
 */
function tcmb_doviz_kuru_render_rate( $code, $atts = array() ) {
	$general = tcmb_doviz_kuru_get_general_options();
	$rates   = tcmb_doviz_kuru_get_rates();

	if ( empty( $rates ) ) {
		return '<span class="tcmb-kur-error">' . esc_html( $general['error_message'] ) . '</span>';
	}

	$defaults = array(
		'decimals'    => $general['decimals'],
		'show_symbol' => $general['show_symbol'] ? 'yes' : 'no',
		'show_flag'   => $general['show_flag'] ? 'yes' : 'no',
		'show_date'   => $general['show_date'] ? 'yes' : 'no',
		'field'       => $general['field'],
	);

	$atts = shortcode_atts( $defaults, $atts, 'tcmb_kur' );

	$field    = sanitize_text_field( $atts['field'] );
	$decimals = max( 0, (int) $atts['decimals'] );

	$value = tcmb_doviz_kuru_get_rate_value( $code, $field );
	if ( null === $value ) {
		return '<span class="tcmb-kur-error">' . esc_html( $general['error_message'] ) . '</span>';
	}

	$symbols = tcmb_doviz_kuru_get_symbols();
	$flags   = tcmb_doviz_kuru_get_flags();

	$symbol = isset( $symbols[ $code ] ) ? $symbols[ $code ] : '';
	$flag   = isset( $flags[ $code ] ) ? $flags[ $code ] : '';

	$show_symbol = ( 'yes' === strtolower( (string) $atts['show_symbol'] ) );
	$show_flag   = ( 'yes' === strtolower( (string) $atts['show_flag'] ) );
	$show_date   = ( 'yes' === strtolower( (string) $atts['show_date'] ) );

	$formatted_value = number_format_i18n( $value, $decimals );

	$date_string = '';
	if ( $show_date && ! empty( $rates['_DATE'] ) ) {
		/* translators: %s: Date string coming from TCMB XML (for example 18.11.2025). */
		$date_string = sprintf( esc_html__( 'TCMB, %s', TCMB_DOVIZ_KURU_TEXTDOMAIN ), esc_html( $rates['_DATE'] ) );
	}

	$output  = '<span class="tcmb-kur tcmb-kur-' . esc_attr( strtolower( $code ) ) . '">';
	if ( $show_flag && $flag ) {
		$output .= '<span class="tcmb-kur-flag" aria-hidden="true">' . esc_html( $flag ) . '</span> ';
	}
	if ( $show_symbol && $symbol ) {
		$output .= '<span class="tcmb-kur-symbol">' . esc_html( $symbol ) . '</span>';
	}
	$output .= '<span class="tcmb-kur-value">' . esc_html( $formatted_value ) . '</span>';
	if ( $date_string ) {
		$output .= ' <small class="tcmb-kur-date">' . esc_html( $date_string ) . '</small>';
	}
	$output .= '</span>';

	return $output;
}

/**
 * Shortcodes: simple aliases.
 */
function tcmb_doviz_kuru_shortcode_usd( $atts ) {
	return tcmb_doviz_kuru_render_rate( 'USD', $atts );
}
add_shortcode( 'dolar-kuru', 'tcmb_doviz_kuru_shortcode_usd' );

function tcmb_doviz_kuru_shortcode_eur( $atts ) {
	return tcmb_doviz_kuru_render_rate( 'EUR', $atts );
}
add_shortcode( 'euro-kuru', 'tcmb_doviz_kuru_shortcode_eur' );

function tcmb_doviz_kuru_shortcode_gbp( $atts ) {
	return tcmb_doviz_kuru_render_rate( 'GBP', $atts );
}
add_shortcode( 'sterlin-kuru', 'tcmb_doviz_kuru_shortcode_gbp' );

function tcmb_doviz_kuru_shortcode_jpy( $atts ) {
	return tcmb_doviz_kuru_render_rate( 'JPY', $atts );
}
add_shortcode( 'yen-kuru', 'tcmb_doviz_kuru_shortcode_jpy' );

function tcmb_doviz_kuru_shortcode_cny( $atts ) {
	return tcmb_doviz_kuru_render_rate( 'CNY', $atts );
}
add_shortcode( 'yuan-kuru', 'tcmb_doviz_kuru_shortcode_cny' );

function tcmb_doviz_kuru_shortcode_aed( $atts ) {
	return tcmb_doviz_kuru_render_rate( 'AED', $atts );
}
add_shortcode( 'dirhem-kuru', 'tcmb_doviz_kuru_shortcode_aed' );

/**
 * Generic shortcode: [tcmb_kur code="USD" field="ForexSelling" decimals="4" show_flag="yes" show_symbol="no" show_date="yes"]
 */
function tcmb_doviz_kuru_shortcode_generic( $atts ) {
	$atts = shortcode_atts(
		array(
			'code'        => 'USD',
			'field'       => 'ForexSelling',
			'decimals'    => '',
			'show_symbol' => '',
			'show_flag'   => '',
			'show_date'   => '',
		),
		$atts,
		'tcmb_kur'
	);

	return tcmb_doviz_kuru_render_rate( strtoupper( $atts['code'] ), $atts );
}
add_shortcode( 'tcmb_kur', 'tcmb_doviz_kuru_shortcode_generic' );

/**
 * Table shortcode: [tcmb_kur_table code="USD,EUR,GBP,JPY,CNY,AED" field="ForexSelling" decimals="4"]
 */
function tcmb_doviz_kuru_shortcode_table( $atts ) {
	$general = tcmb_doviz_kuru_get_general_options();
	$rates   = tcmb_doviz_kuru_get_rates();

	if ( empty( $rates ) ) {
		return '<span class="tcmb-kur-error">' . esc_html( $general['error_message'] ) . '</span>';
	}

	$atts = shortcode_atts(
		array(
			'code'     => 'USD,EUR,GBP,JPY,CNY,AED',
			'field'    => $general['field'],
			'decimals' => $general['decimals'],
		),
		$atts,
		'tcmb_kur_table'
	);

	$field    = sanitize_text_field( $atts['field'] );
	$decimals = max( 0, (int) $atts['decimals'] );
	$codes    = explode( ',', $atts['code'] );
	$codes    = array_map( 'trim', $codes );
	$codes    = array_filter( $codes );

	if ( empty( $codes ) ) {
		return '';
	}

	$symbols = tcmb_doviz_kuru_get_symbols();
	$flags   = tcmb_doviz_kuru_get_flags();

	ob_start();
	?>
	<table class="tcmb-kur-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Currency', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<th><?php esc_html_e( 'Rate', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $codes as $code ) : ?>
				<?php
				$code  = strtoupper( $code );
				$value = tcmb_doviz_kuru_get_rate_value( $code, $field );
				if ( null === $value ) {
					continue;
				}
				$formatted_value = number_format_i18n( $value, $decimals );
				$symbol          = isset( $symbols[ $code ] ) ? $symbols[ $code ] : '';
				$flag            = isset( $flags[ $code ] ) ? $flags[ $code ] : '';
				?>
				<tr class="tcmb-kur-row-<?php echo esc_attr( strtolower( $code ) ); ?>">
					<td>
						<?php if ( $flag ) : ?>
							<span class="tcmb-kur-flag" aria-hidden="true"><?php echo esc_html( $flag ); ?></span>
						<?php endif; ?>
						<strong><?php echo esc_html( $code ); ?></strong>
					</td>
					<td>
						<?php if ( $symbol ) : ?>
							<span class="tcmb-kur-symbol"><?php echo esc_html( $symbol ); ?></span>
						<?php endif; ?>
						<span class="tcmb-kur-value"><?php echo esc_html( $formatted_value ); ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tcmb_kur_table', 'tcmb_doviz_kuru_shortcode_table' );

/**
 * Convert amount between currencies using TRY as pivot.
 *
 * @param float  $amount Amount.
 * @param string $from   Currency code.
 * @param string $to     Currency code.
 *
 * @return float
 */
function tcmb_doviz_kuru_convert_amount( $amount, $from, $to ) {
	$amount = (float) $amount;
	$from   = strtoupper( $from );
	$to     = strtoupper( $to );

	if ( $from === $to ) {
		return $amount;
	}

	$general = tcmb_doviz_kuru_get_general_options();
	$field   = $general['field'];
	$rates   = tcmb_doviz_kuru_get_rates();

	if ( empty( $rates ) ) {
		return $amount;
	}

	// Step 1: from -> TRY.
	if ( 'TRY' === $from ) {
		$amount_in_try = $amount;
	} else {
		$from_rate = tcmb_doviz_kuru_get_rate_value( $from, $field );
		if ( null === $from_rate || $from_rate <= 0 ) {
			return $amount;
		}
		$amount_in_try = $amount * $from_rate;
	}

	// Step 2: TRY -> to.
	if ( 'TRY' === $to ) {
		return $amount_in_try;
	}

	$to_rate = tcmb_doviz_kuru_get_rate_value( $to, $field );
	if ( null === $to_rate || $to_rate <= 0 ) {
		return $amount;
	}

	return $amount_in_try / $to_rate;
}

/* --------------------------------------------------------------------------
 * WooCommerce Integration
 * ----------------------------------------------------------------------- */

if ( ! function_exists( 'tcmb_doviz_kuru_is_wc_active' ) ) {
	function tcmb_doviz_kuru_is_wc_active() {
		return class_exists( 'WooCommerce' );
	}
}

/**
 * Add product currency field (per-product mode).
 */
function tcmb_doviz_kuru_wc_product_currency_field() {
	$currencies = array(
		'TRY' => 'TRY',
		'USD' => 'USD',
		'EUR' => 'EUR',
		'GBP' => 'GBP',
		'JPY' => 'JPY',
		'CNY' => 'CNY',
		'AED' => 'AED',
	);

	echo '<div class="options_group">';

	woocommerce_wp_select(
		array(
			'id'          => '_tcmb_doviz_kuru_product_currency',
			'label'       => esc_html__( 'Ürün Para Birimi (TCMB)', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
			'description' => esc_html__( 'Eğer “Ürün başına para birimi” modu aktifleştirilmişse, bu ürünün fiyatını girerken kullandığınız para birimini seçin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
			'options'     => $currencies,
		)
	);

	echo '</div>';
}
add_action( 'woocommerce_product_options_pricing', 'tcmb_doviz_kuru_wc_product_currency_field' );

/**
 * Save product currency meta.
 */
function tcmb_doviz_kuru_wc_save_product_currency( $product ) {
	if ( isset( $_POST['_tcmb_doviz_kuru_product_currency'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$currency = sanitize_text_field( wp_unslash( $_POST['_tcmb_doviz_kuru_product_currency'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$product->update_meta_data( '_tcmb_doviz_kuru_product_currency', $currency );
	}
}
add_action( 'woocommerce_admin_process_product_object', 'tcmb_doviz_kuru_wc_save_product_currency' );

/**
 * Filter product prices on frontend.
 */
function tcmb_doviz_kuru_wc_filter_price( $price, $product ) {
	if ( is_admin() ) {
		return $price;
	}

	if ( ! tcmb_doviz_kuru_is_wc_active() ) {
		return $price;
	}

	$wc_options = tcmb_doviz_kuru_get_wc_options();

	if ( empty( $wc_options['enabled'] ) ) {
		return $price;
	}

	if ( '' === $price || null === $price ) {
		return $price;
	}

	$price = (float) $price;

	$mode           = $wc_options['mode'];
	$input_currency = strtoupper( $wc_options['input_currency'] );
	$store_currency = strtoupper( $wc_options['store_currency'] );

	if ( 'per_product' === $mode ) {
		$product_currency = $product->get_meta( '_tcmb_doviz_kuru_product_currency' );
		if ( $product_currency ) {
			$input_currency = strtoupper( $product_currency );
		}
	}

	$converted = tcmb_doviz_kuru_convert_amount( $price, $input_currency, $store_currency );

	return $converted;
}
add_filter( 'woocommerce_product_get_price', 'tcmb_doviz_kuru_wc_filter_price', 20, 2 );
add_filter( 'woocommerce_product_get_regular_price', 'tcmb_doviz_kuru_wc_filter_price', 20, 2 );
add_filter( 'woocommerce_product_get_sale_price', 'tcmb_doviz_kuru_wc_filter_price', 20, 2 );

/**
 * Show original currency price on single product.
 */
function tcmb_doviz_kuru_wc_show_original_price( $price_html, $product ) {
	if ( is_admin() ) {
		return $price_html;
	}

	if ( ! tcmb_doviz_kuru_is_wc_active() ) {
		return $price_html;
	}

	$wc_options = tcmb_doviz_kuru_get_wc_options();
	if ( empty( $wc_options['enabled'] ) || empty( $wc_options['show_original_price'] ) ) {
		return $price_html;
	}

	$mode           = $wc_options['mode'];
	$input_currency = strtoupper( $wc_options['input_currency'] );

	if ( 'per_product' === $mode ) {
		$product_currency = $product->get_meta( '_tcmb_doviz_kuru_product_currency' );
		if ( $product_currency ) {
			$input_currency = strtoupper( $product_currency );
		}
	}

	$symbols   = tcmb_doviz_kuru_get_symbols();
	$symbol    = isset( $symbols[ $input_currency ] ) ? $symbols[ $input_currency ] : $input_currency;
	$raw_price = $product->get_meta( '_price' );

	if ( '' === $raw_price || null === $raw_price ) {
		return $price_html;
	}

	$raw_price = (float) $raw_price;
	$formatted = wc_price( $raw_price, array( 'currency' => $input_currency ) );

	/* translators: 1: currency code (for example USD), 2: formatted original price with symbol. */
	$label = sprintf(
		esc_html__( 'Orijinal fiyat (%1$s): %2$s', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
		esc_html( $input_currency ),
		wp_kses_post( $formatted )
	);

	$price_html .= '<br><small class="tcmb-original-price" data-currency="' . esc_attr( $input_currency ) . '">' . wp_kses_post( $label ) . '</small>';

	return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'tcmb_doviz_kuru_wc_show_original_price', 20, 2 );

/* --------------------------------------------------------------------------
 * Admin Pages
 * ----------------------------------------------------------------------- */

/**
 * Add admin menu.
 */
function tcmb_doviz_kuru_admin_menu() {
	add_menu_page(
		__( 'TCMB Döviz Kurları', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
		__( 'TCMB Döviz', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
		'manage_options',
		'tcmb-doviz-kuru',
		'tcmb_doviz_kuru_render_admin_page',
		'dashicons-chart-line',
		65
	);
}
add_action( 'admin_menu', 'tcmb_doviz_kuru_admin_menu' );

/**
 * Render admin page with tabs: intro, settings, woocommerce, faq.
 */
function tcmb_doviz_kuru_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'intro'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$tabs = array(
		'intro'   => __( 'Tanıtım', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
		'settings'=> __( 'Döviz Kur Ayarları', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
		'wc'      => __( 'WooCommerce', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
		'faq'     => __( 'S.S.S.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
	);

	// Handle form submissions.
	if ( isset( $_POST['tcmb_doviz_kuru_save_general'] ) && check_admin_referer( 'tcmb_doviz_kuru_save_general', 'tcmb_doviz_kuru_nonce' ) ) {
		$field         = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : 'ForexSelling';
		$decimals      = isset( $_POST['decimals'] ) ? (int) $_POST['decimals'] : 2; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$show_symbol   = isset( $_POST['show_symbol'] ) ? 1 : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$show_flag     = isset( $_POST['show_flag'] ) ? 1 : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$show_date     = isset( $_POST['show_date'] ) ? 1 : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$cache_minutes = isset( $_POST['cache_minutes'] ) ? (int) $_POST['cache_minutes'] : 60; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$error_message = isset( $_POST['error_message'] ) ? wp_kses_post( wp_unslash( $_POST['error_message'] ) ) : '';

		$options = tcmb_doviz_kuru_get_general_options();

		$options['field']         = $field;
		$options['decimals']      = max( 0, min( 6, $decimals ) );
		$options['show_symbol']   = $show_symbol;
		$options['show_flag']     = $show_flag;
		$options['show_date']     = $show_date;
		$options['cache_minutes'] = max( 1, $cache_minutes );
		if ( $error_message ) {
			$options['error_message'] = $error_message;
		}

		update_option( TCMB_DOVIZ_KURU_OPTION_GENERAL, $options );
		delete_transient( 'tcmb_doviz_kuru_cache' );

		$tab = 'settings';

		/* translators: %s: settings section name. */
		add_settings_error( 'tcmb_doviz_kuru_messages', 'general_saved', sprintf( esc_html__( '%s ayarları kaydedildi.', TCMB_DOVIZ_KURU_TEXTDOMAIN ), esc_html__( 'Döviz Kur Ayarları', TCMB_DOVIZ_KURU_TEXTDOMAIN ) ), 'updated' );
	}

	if ( isset( $_POST['tcmb_doviz_kuru_save_wc'] ) && check_admin_referer( 'tcmb_doviz_kuru_save_wc', 'tcmb_doviz_kuru_wc_nonce' ) ) {
		$enabled             = isset( $_POST['enabled'] ) ? 1 : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$mode                = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : 'single';
		$input_currency      = isset( $_POST['input_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['input_currency'] ) ) : 'USD';
		$store_currency      = isset( $_POST['store_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['store_currency'] ) ) : 'TRY';
		$show_original_price = isset( $_POST['show_original_price'] ) ? 1 : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$options = tcmb_doviz_kuru_get_wc_options();

		$options['enabled']             = $enabled;
		$options['mode']                = in_array( $mode, array( 'single', 'per_product' ), true ) ? $mode : 'single';
		$options['input_currency']      = strtoupper( $input_currency );
		$options['store_currency']      = strtoupper( $store_currency );
		$options['show_original_price'] = $show_original_price;

		update_option( TCMB_DOVIZ_KURU_OPTION_WC, $options );

		$tab = 'wc';

		/* translators: %s: settings section name. */
		add_settings_error( 'tcmb_doviz_kuru_messages', 'wc_saved', sprintf( esc_html__( '%s ayarları kaydedildi.', TCMB_DOVIZ_KURU_TEXTDOMAIN ), esc_html__( 'WooCommerce', TCMB_DOVIZ_KURU_TEXTDOMAIN ) ), 'updated' );
	}

	settings_errors( 'tcmb_doviz_kuru_messages' );

	?>
	<div class="wrap tcmb-doviz-kuru-admin">
		<h1><?php esc_html_e( 'TCMB Döviz Kurları – E-Ticaret ve Elementor', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h1>

		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $id => $label ) : ?>
				<?php
				$class = ( $id === $tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';
				$url   = add_query_arg(
					array(
						'page' => 'tcmb-doviz-kuru',
						'tab'  => $id,
					),
					admin_url( 'admin.php' )
				);
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $class ); ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<div class="tcmb-doviz-kuru-tab-content">
			<?php
			switch ( $tab ) {
				case 'settings':
					tcmb_doviz_kuru_render_tab_settings();
					break;
				case 'wc':
					tcmb_doviz_kuru_render_tab_wc();
					break;
				case 'faq':
					tcmb_doviz_kuru_render_tab_faq();
					break;
				case 'intro':
				default:
					tcmb_doviz_kuru_render_tab_intro();
					break;
			}
			?>
		</div>

		<hr />
		<p>
			<?php
			printf(
				/* translators: %s: Hedef Hosting link. */
				esc_html__( 'Bu eklenti %s tarafından geliştirilmiştir.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
				'<a href="https://hedefhosting.com.tr" target="_blank" rel="noopener noreferrer">Hedef Hosting</a>'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Intro tab.
 */
function tcmb_doviz_kuru_render_tab_intro() {
	?>
	<h2><?php esc_html_e( 'Tanıtım', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h2>
	<p><?php esc_html_e( 'TCMB Döviz Kurları eklentisi, TCMB today.xml verisini kullanarak WordPress sitenizde dinamik döviz kurları göstermenizi sağlar.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'Kısa Kodlar', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<ul>
		<li><code>[dolar-kuru]</code> – USD (Amerikan Doları)</li>
		<li><code>[euro-kuru]</code> – EUR (Euro)</li>
		<li><code>[sterlin-kuru]</code> – GBP (İngiliz Sterlini)</li>
		<li><code>[yen-kuru]</code> – JPY (Japon Yeni)</li>
		<li><code>[yuan-kuru]</code> – CNY (Çin Yuanı)</li>
		<li><code>[dirhem-kuru]</code> – AED (BAE Dirhemi)</li>
		<li><code>[tcmb_kur code="USD" field="ForexSelling" decimals="4" show_flag="yes" show_symbol="no" show_date="yes"]</code></li>
		<li><code>[tcmb_kur_table code="USD,EUR,GBP,JPY,CNY,AED" field="ForexSelling" decimals="4"]</code></li>
	</ul>

	<h3><?php esc_html_e( 'Ortak Parametreler', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<ul>
		<li><code>decimals</code> – Ondalık hane sayısı (varsayılan: genel ayardan)</li>
		<li><code>show_symbol</code> – Sembol göster (yes/no)</li>
		<li><code>show_flag</code> – Bayrak göster (yes/no)</li>
		<li><code>show_date</code> – Tarih göster (yes/no)</li>
	</ul>

	<h3><?php esc_html_e( 'Elementor Widget', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Elementor editöründe “TCMB Döviz” kategorisi altında “TCMB Döviz Kuru” widget\'ını bulabilir, sürükle-bırak ile sayfanıza ekleyebilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
	<?php
}

/**
 * Settings tab: general currency settings.
 */
function tcmb_doviz_kuru_render_tab_settings() {
	$options = tcmb_doviz_kuru_get_general_options();
	?>
	<form method="post">
		<?php wp_nonce_field( 'tcmb_doviz_kuru_save_general', 'tcmb_doviz_kuru_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="field"><?php esc_html_e( 'Varsayılan TCMB Alanı', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<select name="field" id="field">
						<option value="ForexSelling" <?php selected( $options['field'], 'ForexSelling' ); ?>>ForexSelling</option>
						<option value="ForexBuying" <?php selected( $options['field'], 'ForexBuying' ); ?>>ForexBuying</option>
						<option value="BanknoteSelling" <?php selected( $options['field'], 'BanknoteSelling' ); ?>>BanknoteSelling</option>
						<option value="BanknoteBuying" <?php selected( $options['field'], 'BanknoteBuying' ); ?>>BanknoteBuying</option>
					</select>
					<p class="description"><?php esc_html_e( 'Kısa kodlarda alan belirtilmezse kullanılacak varsayılan alan.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="decimals"><?php esc_html_e( 'Ondalık Hane Sayısı', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="number" min="0" max="6" id="decimals" name="decimals" value="<?php echo esc_attr( (int) $options['decimals'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Varsayılan olarak kaç ondalık hane gösterileceğini belirleyin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Sembol Gösterimi', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="show_symbol" value="1" <?php checked( $options['show_symbol'], 1 ); ?> />
						<?php esc_html_e( 'Kur sembolünü (örn. $, €, £) göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Bayrak Gösterimi', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="show_flag" value="1" <?php checked( $options['show_flag'], 1 ); ?> />
						<?php esc_html_e( 'Ülke bayrağını göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Tarih Gösterimi', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="show_date" value="1" <?php checked( $options['show_date'], 1 ); ?> />
						<?php esc_html_e( '“TCMB, tarih” bilgisini göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="cache_minutes"><?php esc_html_e( 'Önbellek Süresi (dakika)', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<input type="number" min="1" id="cache_minutes" name="cache_minutes" value="<?php echo esc_attr( (int) $options['cache_minutes'] ); ?>" />
					<p class="description"><?php esc_html_e( 'TCMB verisi kaç dakika boyunca önbellekte tutulacak.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="error_message"><?php esc_html_e( 'Hata Mesajı', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<textarea name="error_message" id="error_message" rows="3" cols="60"><?php echo esc_textarea( $options['error_message'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'TCMB verisi alınamadığında kısa kodların göstereceği mesaj.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Ayarları Kaydet', TCMB_DOVIZ_KURU_TEXTDOMAIN ), 'primary', 'tcmb_doviz_kuru_save_general' ); ?>
	</form>
	<?php
}

/**
 * WooCommerce tab.
 */
function tcmb_doviz_kuru_render_tab_wc() {
	$options = tcmb_doviz_kuru_get_wc_options();
	$currencies = array(
		'TRY' => 'TRY',
		'USD' => 'USD',
		'EUR' => 'EUR',
		'GBP' => 'GBP',
		'JPY' => 'JPY',
		'CNY' => 'CNY',
		'AED' => 'AED',
	);
	?>
	<form method="post">
		<?php wp_nonce_field( 'tcmb_doviz_kuru_save_wc', 'tcmb_doviz_kuru_wc_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'WooCommerce Entegrasyonu', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="enabled" value="1" <?php checked( $options['enabled'], 1 ); ?> />
						<?php esc_html_e( 'WooCommerce fiyatlarını TCMB kurlarına göre otomatik dönüştür', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Eğer sadece kısa kodları kullanmak istiyorsanız bu seçeneği işaretlemeyin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Fiyat Giriş Modu', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="radio" name="mode" value="single" <?php checked( $options['mode'], 'single' ); ?> />
						<?php esc_html_e( 'Tüm ürünler aynı para biriminde', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label><br />
					<label>
						<input type="radio" name="mode" value="per_product" <?php checked( $options['mode'], 'per_product' ); ?> />
						<?php esc_html_e( 'Ürün başına para birimi seç', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label>
					<p class="description"><?php esc_html_e( '“Ürün başına para birimi” modunda, her ürün için ürün düzenleme ekranından para birimi seçebilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="input_currency"><?php esc_html_e( 'Fiyatları Girdiğiniz Para Birimi', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<select name="input_currency" id="input_currency">
						<?php foreach ( $currencies as $code => $label ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( strtoupper( $options['input_currency'] ), $code ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Tek para birimi modunda tüm ürünleri bu para biriminde fiyatlandırdığınızı varsayar.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="store_currency"><?php esc_html_e( 'Mağazada Görüntülenen Para Birimi', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
				</th>
				<td>
					<select name="store_currency" id="store_currency">
						<?php foreach ( $currencies as $code => $label ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( strtoupper( $options['store_currency'] ), $code ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php esc_html_e( 'WooCommerce → Ayarlar → Genel → Para Birimi ile aynı olmasını öneririz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Orijinal Fiyatı Göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="show_original_price" value="1" <?php checked( $options['show_original_price'], 1 ); ?> />
						<?php esc_html_e( 'Ürün sayfasında orijinal döviz fiyatını küçük not olarak göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'WooCommerce Ayarlarını Kaydet', TCMB_DOVIZ_KURU_TEXTDOMAIN ), 'primary', 'tcmb_doviz_kuru_save_wc' ); ?>
	</form>
	<?php
}

/**
 * FAQ tab (S.S.S.).
 */
function tcmb_doviz_kuru_render_tab_faq() {
	?>
	<h2><?php esc_html_e( 'Sıkça Sorulan Sorular', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h2>

	<h3><?php esc_html_e( 'Bu eklenti ek bir ücretli API kullanıyor mu?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Hayır. Eklenti doğrudan TCMB\'nin resmi today.xml dosyasını HTTP isteği ile çeker. Herhangi bir API anahtarı veya üçüncü parti servis kullanılmaz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'Kurlar ne sıklıkla güncellenir?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Kurlar TCMB\'den çekildikten sonra WordPress önbelleğinde (transient) tutulur. Varsayılan olarak 60 dakika, ayarlar sekmesinden bu süreyi değiştirebilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'Önbelleği manuel olarak temizleyebilir miyim?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Ayarları kaydettiğinizde önbellek otomatik olarak temizlenir. Ayrıca, eklentiyi devre dışı bırakıp tekrar etkinleştirerek de önbelleği sıfırlayabilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'JPY gibi bazı kurlar TCMB\'de 100 birim üzerinden veriliyor. Bu sorun olur mu?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Hayır. Eklenti TCMB XML içindeki Unit alanını okuyup tüm değerleri 1 birim döviz üzerinden normalize eder. Yani JPY, CNY gibi para birimlerinde de doğru hesaplama yapılır.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'WooCommerce entegrasyonunu kullanmak zorunda mıyım?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Hayır. Eklentiyi yalnızca kısa kodlar ve Elementor widget\'ı için kullanabilirsiniz. WooCommerce entegrasyonu tamamen isteğe bağlıdır.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'Tek para birimi ve ürün başına para birimi modlarının farkı nedir?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Tek para birimi modunda tüm ürün fiyatlarını aynı para biriminde (örneğin USD) girersiniz ve eklenti bunları mağaza para birimine (örneğin TRY) çevirir. Ürün başına para birimi modunda ise her ürün için ayrı para birimi seçebilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'Mağaza para birimim ile eklentide seçtiğim mağaza para birimi aynı mı olmalı?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Önerilir. WooCommerce → Ayarlar → Genel → Para Birimi ile eklenti ayarlarında seçtiğiniz mağaza para birimini aynı tutarsanız fiyat biçimlendirme ve ödeme sayfası deneyimi daha tutarlı olur.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'WooCommerce kullanmıyorum. Sadece kuru gösterebilir miyim?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<p><?php esc_html_e( 'Evet. WooCommerce olmadan da kısa kodları ve Elementor widget\'ını kullanarak döviz kurlarını gösterebilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

	<h3><?php esc_html_e( 'Elementor widget\'ını nasıl kullanırım?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'Elementor ile bir sayfayı düzenleyin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></li>
		<li><?php esc_html_e( 'Sol panelde “TCMB Döviz” kategorisini bulun.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></li>
		<li><?php esc_html_e( '“TCMB Döviz Kuru” widget\'ını sürükleyip istediğiniz alana bırakın.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></li>
		<li><?php esc_html_e( 'Widget ayarlarından döviz birimini, TCMB alanını, ondalık hane, sembol, bayrak ve tarih gösterimini seçin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></li>
	</ol>
	<?php
}

/* --------------------------------------------------------------------------
 * Elementor Widget
 * ----------------------------------------------------------------------- */

/**
 * Register Elementor category.
 */
function tcmb_doviz_kuru_elementor_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'tcmb-doviz-kuru-category',
		array(
			'title' => __( 'TCMB Döviz', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
			'icon'  => 'fa fa-money',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'tcmb_doviz_kuru_elementor_register_category' );

/**
 * Elementor widget class.
 */
if ( class_exists( '\Elementor\Widget_Base' ) ) {

	class TCMB_Doviz_Kuru_Elementor_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'tcmb_doviz_kuru_widget';
		}

		public function get_title() {
			return __( 'TCMB Döviz Kuru', TCMB_DOVIZ_KURU_TEXTDOMAIN );
		}

		public function get_icon() {
			return 'eicon-number-field';
		}

		public function get_categories() {
			return array( 'tcmb-doviz-kuru-category' );
		}

		protected function register_controls() {

			$this->start_controls_section(
				'section_content',
				array(
					'label' => __( 'İçerik', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
				)
			);

			$this->add_control(
				'code',
				array(
					'label'   => __( 'Döviz Kodu', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'USD',
					'options' => array(
						'USD' => 'USD',
						'EUR' => 'EUR',
						'GBP' => 'GBP',
						'JPY' => 'JPY',
						'CNY' => 'CNY',
						'AED' => 'AED',
					),
				)
			);

			$this->add_control(
				'field',
				array(
					'label'   => __( 'TCMB Alanı', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'ForexSelling',
					'options' => array(
						'ForexSelling'   => 'ForexSelling',
						'ForexBuying'    => 'ForexBuying',
						'BanknoteSelling'=> 'BanknoteSelling',
						'BanknoteBuying' => 'BanknoteBuying',
					),
				)
			);

			$this->add_control(
				'decimals',
				array(
					'label'   => __( 'Ondalık Hane', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 2,
					'min'     => 0,
					'max'     => 6,
				)
			);

			$this->add_control(
				'show_symbol',
				array(
					'label'        => __( 'Sembol Göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Evet', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'label_off'    => __( 'Hayır', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_flag',
				array(
					'label'        => __( 'Bayrak Göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Evet', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'label_off'    => __( 'Hayır', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'show_date',
				array(
					'label'        => __( 'Tarihi Göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Evet', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'label_off'    => __( 'Hayır', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style',
				array(
					'label' => __( 'Stil', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'value_color',
				array(
					'label'     => __( 'Değer Rengi', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .tcmb-kur-value' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'date_color',
				array(
					'label'     => __( 'Tarih Rengi', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .tcmb-kur-date' => 'color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();

			$atts = array(
				'field'       => isset( $settings['field'] ) ? $settings['field'] : 'ForexSelling',
				'decimals'    => isset( $settings['decimals'] ) ? $settings['decimals'] : 2,
				'show_symbol' => ! empty( $settings['show_symbol'] ) ? 'yes' : 'no',
				'show_flag'   => ! empty( $settings['show_flag'] ) ? 'yes' : 'no',
				'show_date'   => ! empty( $settings['show_date'] ) ? 'yes' : 'no',
			);

			echo tcmb_doviz_kuru_render_rate( strtoupper( $settings['code'] ), $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/**
 * Register Elementor widget.
 */
function tcmb_doviz_kuru_register_elementor_widget( $widgets_manager ) {
	if ( class_exists( 'TCMB_Doviz_Kuru_Elementor_Widget' ) ) {
		$widgets_manager->register( new \TCMB_Doviz_Kuru_Elementor_Widget() );
	}
}
add_action( 'elementor/widgets/register', 'tcmb_doviz_kuru_register_elementor_widget' );

