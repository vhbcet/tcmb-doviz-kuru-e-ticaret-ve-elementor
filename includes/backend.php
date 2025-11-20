<?php

if ( ! defined( 'ABSPATH' ) ) {
        exit;
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
                        'label'       => esc_html__( 'Ürün Para Birimi (TCMB)', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                        'description' => esc_html__( 'Eğer “Ürün başına para birimi” modu aktifleştirilmişse, bu ürünün fiyatını girerken kullandığınız para birimini seçin.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                        'options'     => $currencies,
                )
        );

        echo '</div>';
}

function tcmb_doviz_kuru_wc_save_product_currency( $product ) {
        if ( isset( $_POST['_tcmb_doviz_kuru_product_currency'] ) ) {
                $currency = sanitize_text_field( wp_unslash( $_POST['_tcmb_doviz_kuru_product_currency'] ) );
                $product->update_meta_data( '_tcmb_doviz_kuru_product_currency', $currency );
        }
}

function tcmb_doviz_kuru_register_wc_admin_hooks() {
        static $registered = false;

        if ( $registered ) {
                return;
        }

        if ( ! tcmb_doviz_kuru_is_wc_active() ) {
                return;
        }

        add_action( 'woocommerce_product_options_pricing', 'tcmb_doviz_kuru_wc_product_currency_field' );
        add_action( 'woocommerce_admin_process_product_object', 'tcmb_doviz_kuru_wc_save_product_currency' );

        $registered = true;
}
add_action( 'plugins_loaded', 'tcmb_doviz_kuru_register_wc_admin_hooks' );
add_action( 'woocommerce_loaded', 'tcmb_doviz_kuru_register_wc_admin_hooks' );

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

function tcmb_doviz_kuru_render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
                return;
        }

        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'intro';

        $tabs = array(
                'intro'   => __( 'Tanıtım', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                'settings'=> __( 'Döviz Kur Ayarları', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                'wc'      => __( 'WooCommerce', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                'faq'     => __( 'S.S.S.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
        );

        if ( isset( $_POST['tcmb_doviz_kuru_clear_cache'] ) && check_admin_referer( 'tcmb_doviz_kuru_clear_cache', 'tcmb_doviz_kuru_clear_nonce' ) ) {
                delete_transient( 'tcmb_doviz_kuru_cache' );
                tcmb_doviz_kuru_update_status(
                        array(
                                'last_error'   => '',
                                'last_updated' => current_time( 'mysql' ),
                        )
                );
                tcmb_doviz_kuru_get_rates();

                add_settings_error( 'tcmb_doviz_kuru_messages', 'cache_cleared', __( 'Kur önbelleği temizlendi ve yenilendi.', TCMB_DOVIZ_KURU_TEXTDOMAIN ), 'updated' );
        }

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

                add_settings_error( 'tcmb_doviz_kuru_messages', 'general_saved', sprintf( esc_html__( '%s ayarları kaydedildi.', TCMB_DOVIZ_KURU_TEXTDOMAIN ), esc_html__( 'Döviz Kur Ayarları', TCMB_DOVIZ_KURU_TEXTDOMAIN ) ), 'updated' );
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
                                esc_html__( 'Bu eklenti %s tarafından geliştirilmiştir.', TCMB_DOVIZ_KURU_TEXTDOMAIN ),
                                '<a href="https://hedefhosting.com.tr" target="_blank" rel="noopener noreferrer">Hedef Hosting</a>'
                        );
                        ?>
                </p>
        </div>
        <?php
}

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

function tcmb_doviz_kuru_render_tab_settings() {
        $options = tcmb_doviz_kuru_get_general_options();
        $status  = tcmb_doviz_kuru_get_status();
        ?>
        <div class="tcmb-doviz-kuru-status-box">
                <h3><?php esc_html_e( 'Son TCMB Durumu', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
                <ul>
                        <li><strong><?php esc_html_e( 'Son TCMB tarihi:', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></strong> <?php echo esc_html( $status['last_date'] ? $status['last_date'] : __( 'Bilinmiyor', TCMB_DOVIZ_KURU_TEXTDOMAIN ) ); ?></li>
                        <li><strong><?php esc_html_e( 'Son güncelleme zamanı:', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></strong> <?php echo esc_html( $status['last_updated'] ? $status['last_updated'] : __( 'Bilinmiyor', TCMB_DOVIZ_KURU_TEXTDOMAIN ) ); ?></li>
                        <li><strong><?php esc_html_e( 'Son hata mesajı:', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></strong> <?php echo $status['last_error'] ? esc_html( $status['last_error'] ) : esc_html__( 'Hata yok', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></li>
                </ul>
                <form method="post">
                        <?php wp_nonce_field( 'tcmb_doviz_kuru_clear_cache', 'tcmb_doviz_kuru_clear_nonce' ); ?>
                        <input type="hidden" name="tcmb_doviz_kuru_clear_cache" value="1" />
                        <?php submit_button( __( 'Kur Önbelleğini Temizle ve Yenile', TCMB_DOVIZ_KURU_TEXTDOMAIN ), 'secondary', 'submit', false ); ?>
                </form>
        </div>

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
                                                <?php foreach ( $currencies as $currency => $label ) : ?>
                                                        <option value="<?php echo esc_attr( $currency ); ?>" <?php selected( $options['input_currency'], $currency ); ?>><?php echo esc_html( $label ); ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                </td>
                        </tr>

                        <tr>
                                <th scope="row">
                                        <label for="store_currency"><?php esc_html_e( 'Mağaza Para Birimi (WooCommerce)', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></label>
                                </th>
                                <td>
                                        <select name="store_currency" id="store_currency">
                                                <?php foreach ( $currencies as $currency => $label ) : ?>
                                                        <option value="<?php echo esc_attr( $currency ); ?>" <?php selected( $options['store_currency'], $currency ); ?>><?php echo esc_html( $label ); ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                        <p class="description"><?php esc_html_e( 'WooCommerce fiyatlarının gösterileceği para birimi.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
                                </td>
                        </tr>

                        <tr>
                                <th scope="row"><?php esc_html_e( 'Orijinal Fiyatı Göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="show_original_price" value="1" <?php checked( $options['show_original_price'], 1 ); ?> />
                                                <?php esc_html_e( 'Ürün sayfasında, girilen para birimindeki orijinal fiyatı da göster', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?>
                                        </label>
                                </td>
                        </tr>
                </table>

                <?php submit_button( __( 'WooCommerce Ayarlarını Kaydet', TCMB_DOVIZ_KURU_TEXTDOMAIN ), 'primary', 'tcmb_doviz_kuru_save_wc' ); ?>
        </form>
        <?php
}

function tcmb_doviz_kuru_render_tab_faq() {
        ?>
        <h2><?php esc_html_e( 'Sıkça Sorulan Sorular', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h2>

        <h3><?php esc_html_e( 'Kurlar ne sıklıkla güncellenir?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
        <p><?php esc_html_e( 'TCMB today.xml dosyası her gün yayınlanır. Eklenti, ayarlarda belirttiğiniz süre boyunca veriyi önbellekte tutar.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

        <h3><?php esc_html_e( 'Elementor desteği var mı?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
        <p><?php esc_html_e( 'Evet, eklenti Elementor için özel bir widget içerir. Elementor editöründe “TCMB Döviz” kategorisinde bulabilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

        <h3><?php esc_html_e( 'WooCommerce fiyatları nasıl dönüştürülür?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
        <p><?php esc_html_e( 'WooCommerce entegrasyonunu aktifleştirdiğinizde, girdiğiniz para birimi ayarlara göre otomatik olarak mağaza para birimine dönüştürülür.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>

        <h3><?php esc_html_e( 'Kısa kodlara kendi tasarımımı uygulayabilir miyim?', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></h3>
        <p><?php esc_html_e( 'Evet, oluşturulan HTML sınıfları aracılığıyla kendi CSS stilinizi ekleyebilirsiniz.', TCMB_DOVIZ_KURU_TEXTDOMAIN ); ?></p>
        <?php
}
