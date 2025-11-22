<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! function_exists( 'tcmb_doviz_kuru_is_wc_active' ) ) {
        function tcmb_doviz_kuru_is_wc_active() {
                return class_exists( 'WooCommerce' );
        }
}

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
                        'label'       => esc_html__( 'Ürün Para Birimi (TCMB)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
                        'description' => esc_html__( 'Eğer “Ürün başına para birimi” modu aktifleştirilmişse, bu ürünün fiyatını girerken kullandığınız para birimini seçin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
                        'options'     => $currencies,
                )
        );

        echo '</div>';
}
add_action( 'woocommerce_product_options_pricing', 'tcmb_doviz_kuru_wc_product_currency_field' );

function tcmb_doviz_kuru_wc_save_product_currency( $product ) {
	$nonce = isset( $_POST['woocommerce_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ) : '';

	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'woocommerce_save_data' ) ) {
		return;
	}

	if ( isset( $_POST['_tcmb_doviz_kuru_product_currency'] ) ) {
		$currency = sanitize_text_field( wp_unslash( $_POST['_tcmb_doviz_kuru_product_currency'] ) );
		$product->update_meta_data( '_tcmb_doviz_kuru_product_currency', $currency );
	}
}
add_action( 'woocommerce_admin_process_product_object', 'tcmb_doviz_kuru_wc_save_product_currency' );

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

        $raw_price = $product->get_meta( '_price' );

        if ( '' === $raw_price || null === $raw_price ) {
                return $price_html;
        }

        $raw_price = (float) $raw_price;
        $formatted = wc_price( $raw_price, array( 'currency' => $input_currency ) );

        $label = sprintf(
                /* translators: 1: currency code, 2: price with currency. */
                esc_html__( 'Orijinal fiyat (%1$s): %2$s', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
                esc_html( $input_currency ),
                wp_kses_post( $formatted )
        );

        $price_html .= '<br><small class="tcmb-original-price" data-currency="' . esc_attr( $input_currency ) . '">' . wp_kses_post( $label ) . '</small>';

        return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'tcmb_doviz_kuru_wc_show_original_price', 20, 2 );
