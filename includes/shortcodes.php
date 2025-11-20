<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

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
                $date_string = sprintf( esc_html__( 'TCMB, %s', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ), esc_html( $rates['_DATE'] ) );
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
                                <th><?php esc_html_e( 'Currency', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
                                <th><?php esc_html_e( 'Rate', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
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
