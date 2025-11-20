<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

function tcmb_doviz_kuru_get_rates() {
        $general   = tcmb_doviz_kuru_get_general_options();
        $cache_key = 'tcmb_doviz_kuru_cache';
        $cached    = get_transient( $cache_key );

        if ( ! empty( $cached ) && is_array( $cached ) ) {
                return $cached;
        }

        $response = wp_remote_get(
                'https://www.tcmb.gov.tr/kurlar/today.xml',
                array(
                        'timeout' => 10,
                )
        );

        if ( is_wp_error( $response ) ) {
                return array();
        }

        $body = wp_remote_retrieve_body( $response );

        if ( empty( $body ) ) {
                return array();
        }

        $xml = simplexml_load_string( $body );

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
