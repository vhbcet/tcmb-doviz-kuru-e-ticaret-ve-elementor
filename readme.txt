=== TCMB Döviz Kuru – E-Ticaret & Elementor Entegrasyonlu ===
Contributors: hedefhosting
Tags: tcmb, doviz, currency, exchange, exchange-rate, kur, usd, eur, gbp, jpy, cny, aed, try, woocommerce, elementor, ecommerce
Requires at least: 5.2
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

TCMB'nin resmi today.xml verisini kullanarak USD, EUR, GBP, JPY, CNY ve AED kurlarını çeker. Kısa kodlar, WooCommerce entegrasyonu ve Elementor widget'i ile kolay kullanım.

== Description ==

**TCMB Döviz Kurları – WooCommerce & Elementor**, Türkiye Cumhuriyet Merkez Bankası'nın (TCMB) resmi **today.xml** dosyasını kullanarak döviz kurlarını çeker ve WordPress sitenizde:

* Kısa kodlarla (shortcode) göstermenizi,
* WooCommerce ürün fiyatlarını otomatik olarak **dövizden TL'ye (veya seçtiğiniz mağaza para birimine)** çevirmenizi,
* Elementor içerisinde özel bir **“TCMB Döviz Kuru” widget'i** ile sürükle-bırak kullanımını

sağlayan, ücretsiz API veya anahtar gerektirmeyen bir eklentidir.

**Öne çıkan özellikler:**

* TCMB today.xml'den kur çekme (ekstra, ücretli bir API yok)
* Önbellek (transient) kullanarak istek sayısını azaltma (süre ayarlanabilir)
* Kısa kodlarla kullanım:
  * `[dolar-kuru]` – USD
  * `[euro-kuru]` – EUR
  * `[sterlin-kuru]` – GBP
  * `[yen-kuru]` – JPY
  * `[yuan-kuru]` – CNY
  * `[dirhem-kuru]` – AED
  * `[tcmb_kur]` – kod parametresi ile genel kullanım
  * `[tcmb_kur_table]` – birden fazla kur için tablo gösterimi
* Sembol, bayrak ve tarih gösterimini aç/kapa:
  * `$`, `€`, `£`, `¥`, `د.إ`
  * 🇺🇸, 🇪🇺, 🇬🇧, 🇯🇵, 🇨🇳, 🇦🇪
  * “TCMB, 17.11.2025” gibi tarih etiketi
* Ondalık (küsurat) hane sayısını ayarlanabilir (0–6)
* **WooCommerce entegrasyonu**:
  * Tüm ürünleri tek bir para biriminde gir (örn. USD) → Mağazada TL göster
  * Veya **ürün başına ayrı para birimi** seç (USD, EUR, GBP, JPY, CNY, AED, TRY)
  * Sepet / kasa aşamasında fiyatları TCMB kuruna göre otomatik dönüştür
  * Ürün sayfasında, istenirse “Orijinal fiyat (USD): $10,00” gibi not göster
* **Elementor widget**:
  * “TCMB Döviz” kategorisi altında “TCMB Döviz Kuru” widget’i
  * Elementor panelinden:
    * Döviz birimi (USD, EUR, GBP, JPY, CNY, AED)
    * TCMB alanı (ForexSelling, ForexBuying, BanknoteSelling, BanknoteBuying)
    * Ondalık hane
    * Bayrak/kur sembolü göster/gizle
    * Tarih göster/gizle
  * Elementor Style sekmesinden tipografi ve renk ayarı

Eklentinin geliştiricisi: **[Hedef Hosting](https://hedefhosting.com.tr)**

---

== Installation ==

1. **Eklentiyi yükleyin**

   * `tcmb-doviz-kuru` klasörünü `wp-content/plugins` dizinine yükleyin  
   **veya**
   * WordPress panelinden `Eklentiler → Yeni Ekle` diyerek zip dosyasını yükleyin.

2. **Etkinleştirin**

   * WordPress yönetim panelinde `Eklentiler` sayfasına gidin,  
   * “TCMB Döviz Kurları – WooCommerce & Elementor” eklentisini bulun ve **Etkinleştir**'e tıklayın.

3. **Ayarları yapın**

   * Sol menüde **TCMB Döviz** menüsü oluşur.
   * **Tanıtım** sekmesinde kısa kod örneklerini görebilirsiniz.
   * **Döviz Kur Ayarları** sekmesinde:
     * TCMB alanı (ForexSelling, ForexBuying vb.),
     * Varsayılan ondalık hane,
     * Hata mesajı,
     * Sembol/Bayrak/Tarih gösterimi,
     * Önbellek süresi (dakika)
     ayarlarını yapın.
   * **WooCommerce** sekmesinde (opsiyonel):
     * WooCommerce entegrasyonunu açın/kapatın,
     * Fiyatları hangi para biriminde girdiğinizi (USD/EUR/TRY…),
     * Mağazada hangi para biriminde göstermek istediğinizi,
     * Tek para birimi mi, ürün başına para birimi mi kullanacağınızı
     belirleyin.

4. **Elementor kullanıyorsanız**

   * Elementor editörde sol panelde **“TCMB Döviz”** kategorisini göreceksiniz.
   * İçinde **“TCMB Döviz Kuru”** widget’i vardır.
   * Widget’i sayfaya sürükleyip bırakarak döviz kuru gösterebilirsiniz.

---

== Frequently Asked Questions ==

= Bu eklenti ücretli bir API kullanıyor mu? =

Hayır. Eklenti, TCMB'nin resmi **today.xml** dosyasını HTTP isteğiyle çeker. Herhangi bir API anahtarı gerektirmez; ekstra ücretli/limitli bir servis yoktur.

= Kurlar ne sıklıkla güncellenir? =

Kurlar çekildikten sonra WordPress önbelleğinde (**transient**) tutulur. Varsayılan süre **60 dakikadır**, ancak “TCMB Döviz → Döviz Kur Ayarları” sayfasında 5–1440 dakika arasında değiştirebilirsiniz.

= Önbelleği manuel olarak temizleyebilir miyim? =

Evet. “Döviz Kur Ayarları” sayfasındaki **“Kur Önbelleğini Temizle ve Yenile”** butonuna tıklayarak TCMB verisini yeniden alabilirsiniz.

= TCMB bağlantısında hata olduğunda nereden görebilirim? =

“Döviz Kur Ayarları” sayfasının üstündeki **“Son TCMB Durumu”** kutusunda:

* Son TCMB tarihi,
* Son güncelleme zamanı,
* Son hata mesajı (varsa)

gösterilir.

= JPY gibi bazı kurlar TCMB’de 100 birim üzerinden veriliyor. Bu sorun çıkarır mı? =

Hayır. Eklenti TCMB XML içindeki **Unit** alanını okur ve daima **1 birim döviz** esas alınarak hesaplama yapar. Böylece JPY, CNY gibi para birimlerinde de WooCommerce dönüşümleri ve kısa kod çıktıları doğru olur.

= WooCommerce entegrasyonunu kullanmak zorunda mıyım? =

Hayır. Eklentiyi sadece **kısa kod için** kullanabilirsiniz. WooCommerce entegrasyonu tamamen opsiyonel.

= Tek para birimi ve ürün başına para birimi modunun farkı nedir? =

* **Tek para birimi (single)**: Tüm ürün fiyatlarını aynı para biriminde (örneğin USD) girersiniz. Eklenti tümünü seçtiğiniz mağaza para birimine (örneğin TRY) çevirir.
* **Ürün başına (per_product)**: Her ürün için WooCommerce ürün düzenleme ekranında “Ürün Para Birimi (TCMB)” alanından ayrı para birimi seçebilirsiniz (USD/EUR/GBP/JPY/CNY/AED/TRY). Eklenti her ürünün kendi kurunu kullanır.

= Mağaza para birimim ile eklentide seçtiğim çıktı para birimi aynı mı olmalı? =

Önerilen, WooCommerce → Ayarlar → Genel → **Para Birimi** ile eklentideki **“Mağaza Para Birimi (Görüntülenen)”** ayarının aynı olmasıdır (örneğin ikisi de TRY). Böylece fiyat formatlama ve ödeme sayfası daha tutarlı olur.

= Sadece kuru göstermek istiyorum, WooCommerce kullanmıyorum. Mümkün mü? =

Evet. WooCommerce’i hiç kullanmasanız bile kısa kodlarla kuru gösterebilirsiniz:

* `[dolar-kuru]`
* `[euro-kuru show_flag="yes"]`
* `[yen-kuru decimals="3" show_symbol="no"]`
* `[tcmb_kur code="EUR" field="ForexBuying" decimals="4"]`
* `[tcmb_kur_table codes="USD,EUR,GBP,JPY,CNY,AED" field="ForexSelling" decimals="4"]`

= Elementor widget’i nasıl kullanılır? =

1. Elementor ile sayfayı açın.
2. Sol panelde “TCMB Döviz” kategorisini bulun.
3. “TCMB Döviz Kuru” widget’ini sürükleyip sayfaya bırakın.
4. Widget ayarlarından:
   * Döviz birimi (USD/EUR/GBP/JPY/CNY/AED),
   * TCMB alanı (ForexSelling vb.),
   * Ondalık hane,
   * Bayrak/sembol/tarih gösterimi
   seçeneklerini ayarlayın.
5. Style sekmesinden yazı tipi, boyut ve renkleri belirleyin.

---

== Shortcode Usage ==

**Temel kısa kodlar:**

* `[dolar-kuru]` – USD (Amerikan Doları)
* `[euro-kuru]` – EUR (Euro)
* `[sterlin-kuru]` – GBP (İngiliz Sterlini)
* `[yen-kuru]` – JPY (Japon Yeni)
* `[yuan-kuru]` – CNY (Çin Yuanı)
* `[dirhem-kuru]` – AED (Birleşik Arap Emirlikleri Dirhemi, Dirhem)

**Ortak parametreler:**

* `decimals` – Ondalık hane sayısı  
  Örnek: `[dolar-kuru decimals="3"]`
* `show_date` – Tarih göster (yes/no)  
  Örnek: `[euro-kuru show_date="yes"]`
* `show_flag` – Bayrak göster (yes/no)  
  Örnek: `[sterlin-kuru show_flag="yes"]`
* `show_symbol` – Sembol göster (yes/no)  
  Örnek: `[yen-kuru show_symbol="no"]`

**Gelişmiş:**

* `[tcmb_kur code="EUR" field="ForexBuying" decimals="4"]`
* `[tcmb_kur_table codes="USD,EUR,GBP,JPY,CNY,AED" field="ForexSelling" decimals="4"]`

---

== Screenshots ==

1. **Döviz Kur Ayarları** – TCMB alanı, ondalık hane, bayrak/sembol/tarih ve önbellek süresi.
2. **WooCommerce Ayarları** – Tek para birimi / ürün başına para birimi modları ve giriş/çıkış para birimi ayarları.
3. **Elementor Widget** – “TCMB Döviz Kuru” widget'inin Elementor panelindeki görünümü ve ön izleme.

---

== Changelog ==

= 2.3.1 =
* Elementor için “TCMB Döviz Kuru” widget’i eklendi.
* JPY (Japon Yeni), CNY (Çin Yuanı) ve AED (BAE Dirhemi) desteği eklendi.
* WooCommerce ürün başına para birimi modu geliştirildi.
* Sembol ve bayrak gösterimi için ayrı ayar seçenekleri eklendi.
* Admin arayüzü, tanıtım sayfası ve S.S.S. bölümü güncellendi.

= 2.2.0 =
* WooCommerce entegrasyonu: sabit giriş para biriminden mağaza para birimine otomatik dönüşüm.
* Ürün sayfasında “orijinal döviz fiyatı” gösterimi eklendi.
* Önbellek süresi ayarlanabilir hale getirildi.

= 2.1.0 =
* Genel `[tcmb_kur]` ve `[tcmb_kur_table]` kısa kodları eklendi.
* Sembol ve tarih gösterimi için temel ayarlar eklendi.

= 1.0.0 =
* İlk sürüm: USD, EUR, GBP kurları için kısa kodlar ve basit ayarlar.

---

== Upgrade Notice ==

= 2.3.1 =
Elementor widget’i ve ek para birimleri (JPY, CNY, AED) eklendi. WooCommerce entegrasyonu ve admin arayüzü geliştirildi. Yeni özelliklerden yararlanmak için güncellemeniz tavsiye edilir.
