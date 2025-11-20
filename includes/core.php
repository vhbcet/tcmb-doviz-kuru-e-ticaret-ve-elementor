<?php

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

define( 'TCMB_DOVIZ_KURU_OPTION_GENERAL', 'tcmb_doviz_kuru_general_options' );
define( 'TCMB_DOVIZ_KURU_OPTION_WC', 'tcmb_doviz_kuru_wc_options' );

tcmb_doviz_kuru_load_textdomain();
add_action( 'plugins_loaded', 'tcmb_doviz_kuru_load_textdomain' );

function tcmb_doviz_kuru_load_textdomain() {
        load_plugin_textdomain(
                TCMB_DOVIZ_KURU_TEXTDOMAIN,
                false,
                dirname( TCMB_DOVIZ_KURU_BASENAME ) . '/languages'
        );
}

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
                'error_message'  => __( 'Şu anda döviz kuru alınamıyor. Lütfen daha sonra tekrar deneyin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
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

function tcmb_doviz_kuru_get_rate_value( $code, $field ) {
        $rates = tcmb_doviz_kuru_get_rates();
        if ( empty( $rates ) || empty( $rates[ $code ] ) ) {
                return null;
        }

        if ( isset( $rates[ $code ][ $field ] ) ) {
                return (float) $rates[ $code ][ $field ];
        }

        if ( isset( $rates[ $code ]['ForexSelling'] ) ) {
                return (float) $rates[ $code ]['ForexSelling'];
        }

        return null;
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

        if ( 'TRY' === $from ) {
                $amount_in_try = $amount;
        } else {
                $from_rate = tcmb_doviz_kuru_get_rate_value( $from, $field );
                if ( null === $from_rate || $from_rate <= 0 ) {
                        return $amount;
                }
                $amount_in_try = $amount * $from_rate;
        }

        if ( 'TRY' === $to ) {
                return $amount_in_try;
        }

        $to_rate = tcmb_doviz_kuru_get_rate_value( $to, $field );
        if ( null === $to_rate || $to_rate <= 0 ) {
                return $amount;
        }

        return $amount_in_try / $to_rate;
}

if ( ! function_exists( 'tcmb_doviz_kuru_is_wc_active' ) ) {
        function tcmb_doviz_kuru_is_wc_active() {
                return class_exists( 'WooCommerce' );
        }
}
