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

        $symbols   = tcmb_doviz_kuru_get_symbols();
        $symbol    = isset( $symbols[ $input_currency ] ) ? $symbols[ $input_currency ] : $input_currency;
        $raw_price = $product->get_meta( '_price' );

        if ( '' === $raw_price || null === $raw_price ) {
                return $price_html;
        }

        $raw_price = (float) $raw_price;
        $formatted = wc_price( $raw_price, array( 'currency' => $input_currency ) );

        $label = sprintf(
                esc_html__( 'Orijinal fiyat (%1$s): %2$s', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                esc_html( $input_currency ),
                wp_kses_post( $formatted )
        );

        $price_html .= '<br><small class="tcmb-original-price" data-currency="' . esc_attr( $input_currency ) . '">' . wp_kses_post( $label ) . '</small>';

        return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'tcmb_doviz_kuru_wc_show_original_price', 20, 2 );

if ( class_exists( '\\Elementor\\Widget_Base' ) ) {
        class TCMB_Doviz_Kuru_Elementor_Widget extends \Elementor\Widget_Base {
                public function get_name() {
                        return 'tcmb_doviz_kuru_widget';
                }

                public function get_title() {
                        return __( 'TCMB Döviz Kuru', TCMB_DOVIZ_KURU_TEXTDOMAIN );
                }

                public function get_icon() {
                        return 'eicon-editor-list';
                }

                public function get_categories() {
                        return array( 'tcmb-doviz-kuru' );
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
                                        'label'   => __( 'Para Birimi', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                                        'type'    => \Elementor\Controls_Manager::SELECT,
                                        'default' => 'USD',
                                        'options' => array(
                                                'USD' => 'USD',
                                                'EUR' => 'EUR',
                                                'GBP' => 'GBP',
                                                'JPY' => 'JPY',
                                                'CNY' => 'CNY',
                                                'AED' => 'AED',
                                                'TRY' => 'TRY',
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

                        echo tcmb_doviz_kuru_render_rate( strtoupper( $settings['code'] ), $atts );
                }
        }
}

function tcmb_doviz_kuru_register_elementor_widget( $widgets_manager ) {
        if ( class_exists( 'TCMB_Doviz_Kuru_Elementor_Widget' ) ) {
                $widgets_manager->register( new \TCMB_Doviz_Kuru_Elementor_Widget() );
        }
}
add_action( 'elementor/widgets/register', 'tcmb_doviz_kuru_register_elementor_widget' );
