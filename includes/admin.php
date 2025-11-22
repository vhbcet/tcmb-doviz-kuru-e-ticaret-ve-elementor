<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

function tcmb_doviz_kuru_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_tcmb-doviz-kuru' !== $hook_suffix ) {
		return;
	}

	wp_register_style( 'tcmb-doviz-kuru-admin', false );
	wp_enqueue_style( 'tcmb-doviz-kuru-admin' );

	$css = '
		.tcmb-doviz-kuru-admin {
			max-width: 1100px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-header {
			display: flex;
			align-items: flex-start;
			justify-content: space-between;
			margin-bottom: 16px;
			gap: 16px;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-header-main h1 {
			margin: 0 0 4px;
			font-size: 26px;
			font-weight: 700;
			letter-spacing: 0.01em;
			color: #0f172a;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-header-main p.tcmb-doviz-kuru-subtitle {
			margin: 0;
			color: #64748b;
			font-size: 14px;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-badge {
			background: linear-gradient(135deg, #0f766e, #0ea5e9);
			color: #ffffff;
			padding: 6px 14px;
			border-radius: 999px;
			font-size: 12px;
			font-weight: 600;
			display: inline-flex;
			align-items: center;
			gap: 6px;
			white-space: nowrap;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-badge span.tcmb-pill-dot {
			width: 8px;
			height: 8px;
			border-radius: 999px;
			background-color: rgba(255,255,255,0.8);
		}

		.tcmb-doviz-kuru-admin .nav-tab-wrapper {
			margin-top: 16px;
			margin-bottom: 20px;
			border-bottom: none;
		}

		.tcmb-doviz-kuru-admin .nav-tab-wrapper .nav-tab {
			border-radius: 999px;
			border: none;
			background-color: #e2e8f0;
			color: #475569;
			font-weight: 500;
			padding: 8px 16px;
			margin-right: 8px;
			margin-bottom: 6px;
		}

		.tcmb-doviz-kuru-admin .nav-tab-wrapper .nav-tab:hover {
			background-color: #cbd5f5;
			color: #0f172a;
		}

		.tcmb-doviz-kuru-admin .nav-tab-wrapper .nav-tab-active {
			background: linear-gradient(135deg, #0f766e, #0ea5e9);
			color: #ffffff;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-tab-content {
			margin-top: 4px;
		}

		.tcmb-doviz-kuru-admin .tcmb-card {
			background-color: #ffffff;
			border-radius: 14px;
			border: 1px solid #e2e8f0;
			box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
			padding: 22px 22px 18px;
			margin-bottom: 24px;
			position: relative;
			overflow: hidden;
		}

		.tcmb-doviz-kuru-admin .tcmb-card::before {
			content: "";
			position: absolute;
			inset: 0;
			background: radial-gradient(circle at top right, rgba(59,130,246,0.12), transparent 55%);
			opacity: 0.9;
			pointer-events: none;
		}

		.tcmb-doviz-kuru-admin .tcmb-card-inner {
			position: relative;
			z-index: 1;
		}

		.tcmb-doviz-kuru-admin .tcmb-card-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 12px;
			gap: 12px;
		}

		.tcmb-doviz-kuru-admin .tcmb-card-title {
			font-size: 18px;
			font-weight: 600;
			color: #0f172a;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.tcmb-doviz-kuru-admin .tcmb-card-title-pill {
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.06em;
			background-color: #e0f2fe;
			color: #0369a1;
			padding: 3px 8px;
			border-radius: 999px;
			font-weight: 600;
		}

		.tcmb-doviz-kuru-admin .tcmb-card-description {
			margin-top: 4px;
			margin-bottom: 0;
			color: #64748b;
			font-size: 13px;
			max-width: 460px;
		}

		.tcmb-doviz-kuru-admin .tcmb-card-badge {
			background-color: #ecfdf5;
			color: #15803d;
			border-radius: 999px;
			padding: 4px 10px;
			font-size: 11px;
			font-weight: 600;
			display: inline-flex;
			align-items: center;
			gap: 6px;
			white-space: nowrap;
		}

		.tcmb-doviz-kuru-admin .tcmb-dot-green {
			width: 7px;
			height: 7px;
			border-radius: 999px;
			background-color: #22c55e;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-intro-grid {
			display: grid;
			grid-template-columns: minmax(0, 2fr) minmax(0, 3fr);
			gap: 18px;
		}

		@media (max-width: 960px) {
			.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-intro-grid {
				grid-template-columns: minmax(0, 1fr);
			}
		}

		.tcmb-doviz-kuru-admin .tcmb-intro-list {
			list-style: none;
			margin: 0;
			padding: 0;
		}

		.tcmb-doviz-kuru-admin .tcmb-intro-list li {
			margin-bottom: 8px;
			font-size: 13px;
			color: #475569;
		}

		.tcmb-doviz-kuru-admin .tcmb-intro-list code {
			background-color: #0f172a;
			color: #e5f2ff;
			padding: 3px 6px;
			border-radius: 4px;
			font-size: 12px;
		}

		.tcmb-doviz-kuru-admin .tcmb-intro-tag {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			font-size: 12px;
			color: #0f172a;
			background-color: #e0f2fe;
			padding: 4px 9px;
			border-radius: 999px;
			margin-right: 6px;
			margin-bottom: 6px;
		}

		.tcmb-doviz-kuru-admin .tcmb-intro-tag span {
			display: inline-flex;
			align-items: center;
			justify-content: center;
		}

		.tcmb-doviz-kuru-admin .tcmb-tag-dot {
			width: 6px;
			height: 6px;
			border-radius: 999px;
			background-color: #0284c7;
		}

		.tcmb-doviz-kuru-admin .form-table {
			margin-top: 6px;
		}

		.tcmb-doviz-kuru-admin .form-table th {
			padding: 10px 10px 10px 0;
			width: 260px;
		}

		.tcmb-doviz-kuru-admin .form-table td {
			padding: 10px 0 10px 4px;
		}

		.tcmb-doviz-kuru-admin .form-table tr:nth-child(odd) td,
		.tcmb-doviz-kuru-admin .form-table tr:nth-child(odd) th {
			background-color: #f8fafc;
		}

		.tcmb-doviz-kuru-admin .form-table input[type="number"],
		.tcmb-doviz-kuru-admin .form-table input[type="text"],
		.tcmb-doviz-kuru-admin .form-table select,
		.tcmb-doviz-kuru-admin .form-table textarea {
			max-width: 320px;
		}

		.tcmb-doviz-kuru-admin .form-table textarea {
			width: 100%;
			max-width: 520px;
		}

		.tcmb-doviz-kuru-admin .tcmb-field-helper {
			font-size: 11px;
			color: #64748b;
			margin-top: 2px;
		}

		.tcmb-doviz-kuru-admin .tcmb-doviz-kuru-tab-content .submit {
			margin-top: 14px;
		}

		.tcmb-doviz-kuru-admin .tcmb-faq h3 {
			margin-top: 18px;
			margin-bottom: 4px;
			font-size: 15px;
		}

		.tcmb-doviz-kuru-admin .tcmb-faq p,
		.tcmb-doviz-kuru-admin .tcmb-faq ol {
			font-size: 13px;
			color: #475569;
		}

		.tcmb-doviz-kuru-admin .tcmb-footer-note {
			font-size: 12px;
			color: #94a3b8;
		}

		.tcmb-doviz-kuru-admin .tcmb-footer-note a {
			color: #0f766e;
			text-decoration: none;
		}

		.tcmb-doviz-kuru-admin .tcmb-footer-note a:hover {
			color: #0ea5e9;
			text-decoration: underline;
		}
	';

	wp_add_inline_style( 'tcmb-doviz-kuru-admin', $css );
}
add_action( 'admin_enqueue_scripts', 'tcmb_doviz_kuru_admin_assets' );

function tcmb_doviz_kuru_admin_menu() {
	add_menu_page(
		__( 'TCMB Döviz Kurları', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
		__( 'TCMB Döviz', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
		'manage_options',
		'tcmb-doviz-kuru',
		'tcmb_doviz_kuru_render_admin_page',
		'dashicons-chart-line',
		65
	);
}
add_action( 'admin_menu', 'tcmb_doviz_kuru_admin_menu' );

function tcmb_doviz_kuru_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'intro';

	$tabs = array(
		'intro'    => __( 'Tanıtım', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
		'settings' => __( 'Döviz Kur Ayarları', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
		'wc'       => __( 'WooCommerce', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
		'faq'      => __( 'S.S.S.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
	);

        if ( isset( $_POST['tcmb_doviz_kuru_save_general'] ) && check_admin_referer( 'tcmb_doviz_kuru_save_general', 'tcmb_doviz_kuru_nonce' ) ) {
                $field         = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : 'ForexSelling';
                $decimals      = isset( $_POST['decimals'] ) ? (int) $_POST['decimals'] : 2;
                $show_symbol   = isset( $_POST['show_symbol'] ) ? 1 : 0;
                $show_flag     = isset( $_POST['show_flag'] ) ? 1 : 0;
                $show_date     = isset( $_POST['show_date'] ) ? 1 : 0;
                $cache_minutes = isset( $_POST['cache_minutes'] ) ? (int) $_POST['cache_minutes'] : 60;
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

                add_settings_error(
                        'tcmb_doviz_kuru_messages',
                        'general_saved',
                        sprintf(
                                /* translators: %s: settings tab title. */
                                esc_html__( '%s ayarları kaydedildi.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
                                esc_html__( 'Döviz Kur Ayarları', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' )
                        ),
                        'updated'
                );
	}

        if ( isset( $_POST['tcmb_doviz_kuru_save_wc'] ) && check_admin_referer( 'tcmb_doviz_kuru_save_wc', 'tcmb_doviz_kuru_wc_nonce' ) ) {
                $enabled             = isset( $_POST['enabled'] ) ? 1 : 0;
                $mode                = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : 'single';
                $input_currency      = isset( $_POST['input_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['input_currency'] ) ) : 'USD';
                $store_currency      = isset( $_POST['store_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['store_currency'] ) ) : 'TRY';
                $show_original_price = isset( $_POST['show_original_price'] ) ? 1 : 0;

		$options = tcmb_doviz_kuru_get_wc_options();

		$options['enabled']             = $enabled;
		$options['mode']                = in_array( $mode, array( 'single', 'per_product' ), true ) ? $mode : 'single';
		$options['input_currency']      = strtoupper( $input_currency );
		$options['store_currency']      = strtoupper( $store_currency );
		$options['show_original_price'] = $show_original_price;

		update_option( TCMB_DOVIZ_KURU_OPTION_WC, $options );

		$tab = 'wc';

                add_settings_error(
                        'tcmb_doviz_kuru_messages',
                        'wc_saved',
                        sprintf(
                                /* translators: %s: settings tab title. */
                                esc_html__( '%s ayarları kaydedildi.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
                                esc_html__( 'WooCommerce', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' )
                        ),
                        'updated'
                );
	}

	settings_errors( 'tcmb_doviz_kuru_messages' );

	?>
	<div class="wrap tcmb-doviz-kuru-admin">
		<div class="tcmb-doviz-kuru-header">
			<div class="tcmb-doviz-kuru-header-main">
				<h1><?php esc_html_e( 'TCMB Döviz Kurları – E-Ticaret ve Elementor', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h1>
				<p class="tcmb-doviz-kuru-subtitle">
					<?php esc_html_e( 'TCMB today.xml verisiyle WooCommerce ve Elementor için dinamik kur ayarlarını yönetin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
				</p>
			</div>
			<div class="tcmb-doviz-kuru-badge">
				<span class="tcmb-pill-dot"></span>
				<span><?php esc_html_e( 'Aktif', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
				<span>v<?php echo esc_html( TCMB_DOVIZ_KURU_VERSION ); ?></span>
			</div>
		</div>

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
		<p class="tcmb-footer-note">
			<?php
                        printf(
                                esc_html__( 'Bu eklenti %s tarafından geliştirilmiştir.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ),
                                '<a href="https://hedefhosting.com.tr" target="_blank" rel="noopener noreferrer">Hedef Hosting</a>'
                        );
                        ?>
                </p>
        </div>
        <?php
}

function tcmb_doviz_kuru_render_tab_intro() {
	?>
	<div class="tcmb-card">
		<div class="tcmb-card-inner">
			<div class="tcmb-card-header">
				<div>
					<div class="tcmb-card-title">
						<?php esc_html_e( 'TCMB Döviz Kurları eklentisine hoş geldiniz', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
						<span class="tcmb-card-title-pill"><?php esc_html_e( 'Genel Bakış', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
					</div>
					<p class="tcmb-card-description">
						<?php esc_html_e(
							'Bu eklenti, TCMB today.xml verisini kullanarak WordPress sitenizde güncel döviz kurları göstermenizi,
							WooCommerce ürün fiyatlarını otomatik olarak dönüştürmenizi ve Elementor ile görsel kur bileşenleri oluşturmanızı sağlar.',
							'tcmb-doviz-kuru-e-ticaret-ve-elementor'
						); ?>
					</p>
				</div>
				<div class="tcmb-card-badge">
					<span class="tcmb-dot-green"></span>
					<span><?php esc_html_e( 'Gerçek zamanlı kur akışı', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
				</div>
			</div>

			<div class="tcmb-doviz-kuru-intro-grid">
				<div>
					<h3><?php esc_html_e( 'Bu eklentiyi hangi senaryolarda kullanabilirsiniz?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
					<ul class="tcmb-intro-list">
						<li><?php esc_html_e( 'E-ticaret sitenizde ürünleri USD, EUR vb. para biriminden fiyatlayıp mağazada TL olarak göstermek.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><?php esc_html_e( 'Fiyat tabloları, hizmet sayfaları veya blog yazıları içinde güncel döviz kuru göstermek.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><?php esc_html_e( 'Elementor ile tasarladığınız sayfalara şık ve esnek döviz bileşenleri eklemek.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
					</ul>

					<h3><?php esc_html_e( 'Hızlı Başlangıç (3 Adım)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
					<ol class="tcmb-intro-list">
						<li>
							<strong><?php esc_html_e( '1. Döviz Kur Ayarları', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></strong><br>
							<span><?php esc_html_e( '“Döviz Kur Ayarları” sekmesinden varsayılan TCMB alanını (ForexSelling vb.), ondalık hane sayısını ve önbellek süresini belirleyin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
						</li>
						<li>
							<strong><?php esc_html_e( '2. WooCommerce Entegrasyonu (isteğe bağlı)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></strong><br>
							<span><?php esc_html_e( '“WooCommerce” sekmesinden fiyatları hangi para biriminde girdiğinizi ve mağazada hangi para birimini göstereceğinizi seçin. İsterseniz ürün başına para birimi modunu aktif edin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
						</li>
						<li>
							<strong><?php esc_html_e( '3. Kısa Kod veya Elementor ile ekleyin', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></strong><br>
							<span><?php esc_html_e( 'İçerik alanlarına kısa kod ekleyin veya Elementor editöründe “TCMB Döviz Kuru” widget\'ını kullanın.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
						</li>
					</ol>
				</div>

				<div>
					<h3><?php esc_html_e( 'Kısa Kod Örnekleri', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
					<ul class="tcmb-intro-list">
						<li><code>[dolar-kuru]</code> – <?php esc_html_e( 'USD (Amerikan Doları)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><code>[euro-kuru]</code> – <?php esc_html_e( 'EUR (Euro)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><code>[sterlin-kuru]</code> – <?php esc_html_e( 'GBP (İngiliz Sterlini)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><code>[yen-kuru]</code> – <?php esc_html_e( 'JPY (Japon Yeni)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><code>[yuan-kuru]</code> – <?php esc_html_e( 'CNY (Çin Yuanı)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
						<li><code>[dirhem-kuru]</code> – <?php esc_html_e( 'AED (BAE Dirhemi)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
					</ul>

					<h4 style="margin-top:10px;"><?php esc_html_e( 'Gelişmiş tek kur örneği', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h4>
					<p class="tcmb-card-description">
						<code>[tcmb_kur code="USD" field="ForexSelling" decimals="4" show_flag="yes" show_symbol="no" show_date="yes"]</code>
					</p>

					<h4 style="margin-top:10px;"><?php esc_html_e( 'Tablo halinde gösterim', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h4>
					<p class="tcmb-card-description">
						<code>[tcmb_kur_table code="USD,EUR,GBP,JPY,CNY,AED" field="ForexSelling" decimals="4"]</code><br>
						<span><?php esc_html_e( 'Bu kısa kod, seçtiğiniz para birimlerini şık bir tablo halinde listeler.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
					</p>

					<h3 style="margin-top:16px;"><?php esc_html_e( 'Elementor Widget', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
					<p class="tcmb-card-description">
						<?php esc_html_e(
							'Elementor editöründe sol panelde “TCMB Döviz” kategorisini bulun ve “TCMB Döviz Kuru” widget\'ını sürükleyip istediğiniz alana bırakın. Widget ayarlarından döviz kodu, TCMB alanı, ondalık hane, sembol, bayrak ve tarih görünümünü dilediğiniz gibi ayarlayabilirsiniz.',
							'tcmb-doviz-kuru-e-ticaret-ve-elementor'
						); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function tcmb_doviz_kuru_render_tab_settings() {
	$options = tcmb_doviz_kuru_get_general_options();
	?>
	<div class="tcmb-card">
		<div class="tcmb-card-inner">
			<div class="tcmb-card-header">
				<div>
					<div class="tcmb-card-title">
						<?php esc_html_e( 'Döviz Kur Genel Ayarları', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
						<span class="tcmb-card-title-pill"><?php esc_html_e( 'TCMB Alanı · Görünüm · Önbellek', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
					</div>
					<p class="tcmb-card-description">
						<?php esc_html_e( 'Buradan varsayılan TCMB alanını, ondalık hane sayısını, sembol/bayrak/tarih gösterimini ve önbellek süresini yapılandırabilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
					</p>
				</div>
				<div class="tcmb-card-badge">
					<span class="tcmb-dot-green"></span>
					<span><?php esc_html_e( 'Performans dostu önbellek', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
				</div>
			</div>

			<form method="post">
				<?php wp_nonce_field( 'tcmb_doviz_kuru_save_general', 'tcmb_doviz_kuru_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="field"><?php esc_html_e( 'Varsayılan TCMB Alanı', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></label>
						</th>
						<td>
							<select name="field" id="field">
								<option value="ForexSelling" <?php selected( $options['field'], 'ForexSelling' ); ?>>ForexSelling</option>
								<option value="ForexBuying" <?php selected( $options['field'], 'ForexBuying' ); ?>>ForexBuying</option>
								<option value="BanknoteSelling" <?php selected( $options['field'], 'BanknoteSelling' ); ?>>BanknoteSelling</option>
								<option value="BanknoteBuying" <?php selected( $options['field'], 'BanknoteBuying' ); ?>>BanknoteBuying</option>
							</select>
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'Kısa kodlarda alan belirtilmezse kullanılacak varsayılan alan.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="decimals"><?php esc_html_e( 'Ondalık Hane Sayısı', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></label>
						</th>
						<td>
							<input type="number" min="0" max="6" id="decimals" name="decimals" value="<?php echo esc_attr( (int) $options['decimals'] ); ?>" />
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'Varsayılan olarak kaç ondalık hane gösterileceğini belirleyin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Sembol Gösterimi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="show_symbol" value="1" <?php checked( $options['show_symbol'], 1 ); ?> />
								<?php esc_html_e( 'Kur sembolünü (örn. $, €, £) göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Bayrak Gösterimi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="show_flag" value="1" <?php checked( $options['show_flag'], 1 ); ?> />
								<?php esc_html_e( 'Ülke bayrağını göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Tarih Gösterimi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="show_date" value="1" <?php checked( $options['show_date'], 1 ); ?> />
								<?php esc_html_e( '“TCMB, tarih” bilgisini göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cache_minutes"><?php esc_html_e( 'Önbellek Süresi (dakika)', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></label>
						</th>
						<td>
							<input type="number" min="1" id="cache_minutes" name="cache_minutes" value="<?php echo esc_attr( (int) $options['cache_minutes'] ); ?>" />
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'TCMB verisi kaç dakika boyunca önbellekte tutulacak.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="error_message"><?php esc_html_e( 'Hata Mesajı', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></label>
						</th>
						<td>
							<textarea name="error_message" id="error_message" rows="3" cols="60"><?php echo esc_textarea( $options['error_message'] ); ?></textarea>
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'TCMB verisi alınamadığında kısa kodların göstereceği mesaj.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'Ayarları Kaydet', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ), 'primary', 'tcmb_doviz_kuru_save_general' ); ?>
			</form>
		</div>
	</div>
	<?php
}

function tcmb_doviz_kuru_render_tab_wc() {
	$options    = tcmb_doviz_kuru_get_wc_options();
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
	<div class="tcmb-card">
		<div class="tcmb-card-inner">
			<div class="tcmb-card-header">
				<div>
					<div class="tcmb-card-title">
						<?php esc_html_e( 'WooCommerce Döviz Dönüşümü', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
						<span class="tcmb-card-title-pill"><?php esc_html_e( 'Fiyat Girişi · Mağaza Para Birimi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
					</div>
					<p class="tcmb-card-description">
						<?php esc_html_e( 'WooCommerce ürün fiyatlarını TCMB kurlarına göre otomatik dönüştürmek için bu ayarları kullanın. Tek para birimi veya ürün başına para birimi modları arasında seçim yapabilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
					</p>
				</div>
				<div class="tcmb-card-badge">
					<span class="tcmb-dot-green"></span>
					<span><?php esc_html_e( 'WooCommerce entegrasyonu', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
				</div>
			</div>

			<form method="post">
				<?php wp_nonce_field( 'tcmb_doviz_kuru_save_wc', 'tcmb_doviz_kuru_wc_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'WooCommerce Entegrasyonu', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="enabled" value="1" <?php checked( $options['enabled'], 1 ); ?> />
								<?php esc_html_e( 'WooCommerce fiyatlarını TCMB kurlarına göre otomatik dönüştür', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label>
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'Eğer sadece kısa kodları kullanmak istiyorsanız bu seçeneği işaretlemeyin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Fiyat Giriş Modu', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
						<td>
							<label>
								<input type="radio" name="mode" value="single" <?php checked( $options['mode'], 'single' ); ?> />
								<?php esc_html_e( 'Tüm ürünler aynı para biriminde', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label><br />
							<label>
								<input type="radio" name="mode" value="per_product" <?php checked( $options['mode'], 'per_product' ); ?> />
								<?php esc_html_e( 'Ürün başına para birimi seç', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label>
							<p class="description tcmb-field-helper">
								<?php esc_html_e( '“Ürün başına para birimi” modunda, her ürün için ürün düzenleme ekranından para birimi seçebilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="input_currency"><?php esc_html_e( 'Fiyatları Girdiğiniz Para Birimi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></label>
						</th>
						<td>
							<select name="input_currency" id="input_currency">
								<?php foreach ( $currencies as $code => $label ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>" <?php selected( strtoupper( $options['input_currency'] ), $code ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'Tek para birimi modunda tüm ürünleri bu para biriminde fiyatlandırdığınızı varsayar.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="store_currency"><?php esc_html_e( 'Mağazada Görüntülenen Para Birimi', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></label>
						</th>
						<td>
							<select name="store_currency" id="store_currency">
								<?php foreach ( $currencies as $code => $label ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>" <?php selected( strtoupper( $options['store_currency'] ), $code ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<p class="description tcmb-field-helper">
								<?php esc_html_e( 'WooCommerce → Ayarlar → Genel → Para Birimi ile aynı olmasını öneririz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Orijinal Fiyatı Göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="show_original_price" value="1" <?php checked( $options['show_original_price'], 1 ); ?> />
								<?php esc_html_e( 'Ürün sayfasında orijinal döviz fiyatını küçük not olarak göster', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'WooCommerce Ayarlarını Kaydet', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ), 'primary', 'tcmb_doviz_kuru_save_wc' ); ?>
			</form>
		</div>
	</div>
	<?php
}

function tcmb_doviz_kuru_render_tab_faq() {
	?>
	<div class="tcmb-card">
		<div class="tcmb-card-inner tcmb-faq">
			<div class="tcmb-card-header">
				<div>
					<div class="tcmb-card-title">
						<?php esc_html_e( 'Sıkça Sorulan Sorular', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
						<span class="tcmb-card-title-pill"><?php esc_html_e( 'Kur · WooCommerce · Elementor', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></span>
					</div>
					<p class="tcmb-card-description">
						<?php esc_html_e( 'Eklentiyi kullanırken aklınıza gelebilecek temel soruların cevaplarını aşağıda bulabilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?>
					</p>
				</div>
			</div>

			<h3><?php esc_html_e( 'Bu eklenti ek bir ücretli API kullanıyor mu?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Hayır. Eklenti doğrudan TCMB\'nin resmi today.xml dosyasını HTTP isteği ile çeker. Herhangi bir API anahtarı veya üçüncü parti servis kullanılmaz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'Kurlar ne sıklıkla güncellenir?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Kurlar TCMB\'den çekildikten sonra WordPress önbelleğinde (transient) tutulur. Varsayılan olarak 60 dakika, ayarlar sekmesinden bu süreyi değiştirebilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'Önbelleği manuel olarak temizleyebilir miyim?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Ayarları kaydettiğinizde önbellek otomatik olarak temizlenir. Ayrıca, eklentiyi devre dışı bırakıp tekrar etkinleştirerek de önbelleği sıfırlayabilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'JPY gibi bazı kurlar TCMB\'de 100 birim üzerinden veriliyor. Bu sorun olur mu?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Hayır. Eklenti TCMB XML içindeki Unit alanını okuyup tüm değerleri 1 birim döviz üzerinden normalize eder. Yani JPY, CNY gibi para birimlerinde de doğru hesaplama yapılır.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'WooCommerce entegrasyonunu kullanmak zorunda mıyım?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Hayır. Eklentiyi yalnızca kısa kodlar ve Elementor widget\'ı için kullanabilirsiniz. WooCommerce entegrasyonu tamamen isteğe bağlıdır.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'Tek para birimi ve ürün başına para birimi modlarının farkı nedir?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Tek para birimi modunda tüm ürün fiyatlarını aynı para biriminde (örneğin USD) girersiniz ve eklenti bunları mağaza para birimine (örneğin TRY) çevirir. Ürün başına para birimi modunda ise her ürün için ayrı para birimi seçebilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'Mağaza para birimim ile eklentide seçtiğim mağaza para birimi aynı mı olmalı?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Önerilir. WooCommerce → Ayarlar → Genel → Para Birimi ile eklenti ayarlarında seçtiğiniz mağaza para birimini aynı tutarsanız fiyat biçimlendirme ve ödeme sayfası deneyimi daha tutarlı olur.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'WooCommerce kullanmıyorum. Sadece kuru gösterebilir miyim?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<p><?php esc_html_e( 'Evet. WooCommerce olmadan da kısa kodları ve Elementor widget\'ını kullanarak döviz kurlarını gösterebilirsiniz.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></p>

			<h3><?php esc_html_e( 'Elementor widget\'ını nasıl kullanırım?', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></h3>
			<ol>
				<li><?php esc_html_e( 'Elementor ile bir sayfayı düzenleyin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
				<li><?php esc_html_e( 'Sol panelde “TCMB Döviz” kategorisini bulun.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
				<li><?php esc_html_e( '“TCMB Döviz Kuru” widget\'ını sürükleyip istediğiniz alana bırakın.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
				<li><?php esc_html_e( 'Widget ayarlarından döviz birimini, TCMB alanını, ondalık hane, sembol, bayrak ve tarih gösterimini seçin.', 'tcmb-doviz-kuru-e-ticaret-ve-elementor' ); ?></li>
			</ol>
		</div>
	</div>
	<?php
}

