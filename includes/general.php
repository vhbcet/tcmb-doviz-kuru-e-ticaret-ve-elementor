<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

function tcmb_doviz_kuru_activate() {
        $general_defaults = array(
                'field'          => 'ForexSelling',
                'decimals'       => 2,
                'show_symbol'    => 1,
                'show_flag'      => 0,
                'show_date'      => 1,
                'cache_minutes'  => 60,
                'error_message'  => __( 'Şu anda döviz kuru alınamıyor. Lütfen daha sonra tekrar deneyin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
        );

        $wc_defaults = array(
                'enabled'             => 0,
                'mode'                => 'single',
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

function tcmb_doviz_kuru_get_general_options() {
        $defaults = array(
                'field'          => 'ForexSelling',
                'decimals'       => 2,
                'show_symbol'    => 1,
                'show_flag'      => 0,
                'show_date'      => 1,
                'cache_minutes'  => 60,
                'error_message'  => __( 'Şu anda döviz kuru alınamıyor. Lütfen daha sonra tekrar deneyin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
        );

        $options = get_option( TCMB_DOVIZ_KURU_OPTION_GENERAL, array() );

        return wp_parse_args( $options, $defaults );
}

function tcmb_doviz_kuru_get_wc_options() {
        $defaults = array(
                'enabled'             => 0,
                'mode'                => 'single',
                'input_currency'      => 'USD',
                'store_currency'      => 'TRY',
                'show_original_price' => 1,
        );

        $options = get_option( TCMB_DOVIZ_KURU_OPTION_WC, array() );

        return wp_parse_args( $options, $defaults );
}

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
