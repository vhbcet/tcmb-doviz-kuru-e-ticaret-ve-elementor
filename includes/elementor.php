<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

function tcmb_doviz_kuru_elementor_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'tcmb-doviz-kuru-category',
		array(
			'title' => __( 'TCMB Döviz', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
			'icon'  => 'fa fa-money',
		)
	);
}

function tcmb_doviz_kuru_define_elementor_widget_class() {
        if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
                return;
        }

        if ( class_exists( 'TCMB_Doviz_Kuru_Elementor_Widget' ) ) {
                return;
        }

	class TCMB_Doviz_Kuru_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tcmb_doviz_kuru_widget';
	}

	public function get_title() {
		return __( 'TCMB Döviz Kuru', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' );
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
				'label' => __( 'İçerik', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
			)
		);

		$this->add_control(
			'code',
			array(
				'label'   => __( 'Döviz Kodu', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
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
				'label'   => __( 'TCMB Alanı', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ForexSelling',
				'options' => array(
					'ForexSelling'    => 'ForexSelling',
					'ForexBuying'     => 'ForexBuying',
					'BanknoteSelling' => 'BanknoteSelling',
					'BanknoteBuying'  => 'BanknoteBuying',
				),
			)
		);

		$this->add_control(
			'decimals',
			array(
				'label'   => __( 'Ondalık Hane', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => '',
				'min'     => 0,
				'max'     => 6,
				'description' => __( 'Boş bırakırsanız genel ayardaki ondalık hane kullanılır.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
			)
		);

		$this->add_control(
			'show_symbol',
			array(
				'label'        => __( 'Sembol Göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Evet', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'label_off'    => __( 'Hayır', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_flag',
			array(
				'label'        => __( 'Bayrak Göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Evet', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'label_off'    => __( 'Hayır', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => __( 'Tarihi Göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Evet', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'label_off'    => __( 'Hayır', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

                $this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Stil', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

                $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'general_typography',
				'label'    => __( 'Genel Yazı Tipi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'selector' => '{{WRAPPER}} .tcmb-kur',
			)
		);

                $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'label'    => __( 'Kur Değeri Yazı Tipi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'selector' => '{{WRAPPER}} .tcmb-kur-value',
			)
		);

                $this->add_control(
			'value_color',
			array(
				'label'     => __( 'Değer Rengi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tcmb-kur-value' => 'color: {{VALUE}};',
				),
			)
		);

                $this->add_control(
			'symbol_color',
			array(
				'label'     => __( 'Sembol Rengi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .tcmb-kur-symbol' => 'color: {{VALUE}};',
				),
			)
		);

                $this->add_control(
			'date_color',
			array(
				'label'     => __( 'Tarih Rengi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
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
			'field'       => ! empty( $settings['field'] ) ? $settings['field'] : 'ForexSelling',
			'show_symbol' => ! empty( $settings['show_symbol'] ) ? 'yes' : 'no',
			'show_flag'   => ! empty( $settings['show_flag'] ) ? 'yes' : 'no',
			'show_date'   => ! empty( $settings['show_date'] ) ? 'yes' : 'no',
		);

                if ( isset( $settings['decimals'] ) && $settings['decimals'] !== '' && $settings['decimals'] !== null ) {
                        $atts['decimals'] = (int) $settings['decimals'];
                }

                echo tcmb_doviz_kuru_render_rate( strtoupper( $settings['code'] ), $atts );
        }
}

}

function tcmb_doviz_kuru_register_elementor_widget( $widgets_manager ) {
        tcmb_doviz_kuru_define_elementor_widget_class();

        if ( class_exists( 'TCMB_Doviz_Kuru_Elementor_Widget' ) ) {
                $widgets_manager->register( new \TCMB_Doviz_Kuru_Elementor_Widget() );
        }
}

add_action( 'elementor/elements/categories_registered', 'tcmb_doviz_kuru_elementor_register_category' );
add_action( 'elementor/widgets/register', 'tcmb_doviz_kuru_register_elementor_widget' );
add_action( 'elementor/widgets/widgets_registered', 'tcmb_doviz_kuru_register_elementor_widget' );
