Laravel 8 için Seopack
=========

[![Latest Stable Version](https://poser.pugx.org/msbeden/seopack/v/stable.svg)](https://packagist.org/packages/msbeden/seopack) [![Total Downloads](https://poser.pugx.org/msbeden/seopack/downloads.svg)](https://packagist.org/packages/msbeden/seopack) [![Latest Unstable Version](https://poser.pugx.org/msbeden/seopack/v/unstable.svg)](https://packagist.org/packages/msbeden/seopack) [![License](https://poser.pugx.org/msbeden/seopack/license.svg)](https://packagist.org/packages/msbeden/seopack)

Kurulum (Laravel 8.x için)
-----------

* Öncelikle `composer.json` dosyanızdaki `require` kısmına aşağıdaki değeri ekleyin:

    ```json
    "msbeden/seopack": "dev-main"
    ```

  Alternatif olarak `composer require msbeden/seopack` komutu ile de paketi ekleyebilirsiniz.
* Ardından eğer `composer.json` dosyasını elinizle güncellediyseniz kodları projenize dahil etmek için Composer paketlerinizi güncellemelisiniz. `composer update` komutu ile bunu yapabilirsiniz.
* Şimdi de `config/app.php` dosyasını açın, `providers` dizisi içine en alta şunu girin:

    ```php
    msbeden\Seopack\SeopackServiceProvider::class,
    ```
  _(Laravel 5.5 ve sonrası için gerekli değildir)_

* Şimdi yine aynı dosyada `aliases` dizisi altına şu değeri girin:

    ```php
    'Seopack' => msbeden\Seopack\Facades\Seopack::class,
    ```
  _(Laravel 5.5 ve sonrası için gerekli değildir)_

* Şimdi de environment'ınıza konfigürasyon dosyasını paylaşmalısınız. Bunun için aşağıdaki komutu çalıştırın:

    ```shell
    php artisan vendor:publish
    ```
* `config/seopack.php` dosyası paylaşılacak. Burada sabit değerli sayfalarınız için meta, opengraph ve twittercard etiketlerini doldurmalısınız.


Kullanım
-------------
### Meta etiketlerini kullanmak için
#### İlgili Controller'a eklenir ve blade'e gönderilir.
```php
$meta = Seopack::Meta([
    'title'         => 'Sayfa başlığı',
    'description'   => 'Sayfa açıklaması',
    'keywords'      => 'Sayfa anahtar kelimeler',
    'author'        => 'Yazar',
    'publisher'     => 'Yayıncı',
    'robots'        => 'nofollow,noindex' //robot ayarları
]);

return view('frontend.index.index', compact('meta'));
```

#### Blade'de kullanımı
```html
<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    {!! $meta !!}
  </head>
```
___

### Opengraph etiketlerini kullanmak için
#### İlgili Controller'a eklenir ve blade'e gönderilir.
```php
$og = Seopack::OpenGraph([
    'app_id'        => 'Facebook uygulama id',
    'type'          => 'Sayfa tipi', //article
    'site_name'     => 'Site İsmi',
    'title'         => 'Sayfa başlığı',
    'description'   => 'Sayfa açıklaması',
    'url'           => 'Sayfa adresi',
    'image'         => 'Sayfa resmi',
    'image:width'   => 'Sayfa resmi genişliği',
    'image:height'  => 'Sayfa resmi yüksekliği',
    'published_time'=> 'Yayınlanma zamanı',
    'author'        => 'Yazar',
]);

return view('frontend.index.index', compact('og'));
```

#### Blade'de kullanımı
```html
<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    {!! $og !!}
  </head>
```
___

### TwitterCard etiketlerini kullanmak için
#### İlgili Controller'a eklenir ve blade'e gönderilir.
```php
$tw = Seopack::TwitterCard([
    'site'          => 'Site İsmi',
    'title'         => 'Sayfa başlığı',
    'description'   => 'Sayfa açıklaması',
    'image'         => 'Sayfa resmi',
]);

return view('frontend.index.index', compact('tw'));
```

#### Blade'de kullanımı
```html
<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    {!! $tw !!}
  </head>
```
___
### Sitemap kullanmak için
#### İlgili Controller'a eklenir ve çalıştırılır.
Cron atanarak otomatik sitemap üretimi de yapılabilir. Kullanım aşağıdaki gibi sırasıyla eksiksiz olmalıdır.

```php
//SitemapRoot bu metod robots.txt dosyası için gereklidir.
Seopack::SitemapRoot("https://www.msbeden.tk/");

//Site haritası için linkler bu şekilde eklenmelidir.
$urls = array(
    array("https://www.msbeden.tk/", date('c')),
    array("https://www.msbeden.tk/blog", date('c'))
);

//Yukarıda tanımlanan linkler ayrıştırılıyor.
Seopack::SitemapUrls($urls);

//Site haritası oluşturuluyor.
Seopack::SitemapGenerate();

//Site haritası sitemap.xml dosyası şeklinde çıkartılıyor.
Seopack::SitemapPrint();

//Gerekli düzeltmeler robots.txt dosyasına yansıtılıyor.
Seopack::SitemapRobotsUpdate();
```
___
## Sabit sayfalarınız için kullanım
`config/seopack.php` dosyası paylaşılacak. Burada sabit değerli sayfalarınız için meta, opengraph ve twittercard etiketlerini doldurmalısınız.
Örnek olarak meta etiketlerinin sabit kullanımına bakacak olursak;

```php
// Genel Ayarlar
    'standart_meta_acik'        => true, // true olmalıdır.
    'standart_opengraph_acik'   => false,
    'standart_twittercard_acik' => false,

    //Meta Tags
    'meta' => [
        'title'         => 'Genel Site Başlığım',
        'description'   => 'Genel Site Açıklamam',
        'keywords'      => 'Genel Site Anahtar Kelimeler',
        'author'        => 'Yazar',
        'publisher'     => 'Yayıncı',
        'robots'        => ''
    ],

```
### İlgili Controllerda kullanımı:
`Meta` metodunu boş bıraktığınızda `config/seopack.php` dosyasına gider ve `standart_meta_acik` değişkeni true
ise meta etiketleri buradan alınır. Boş bırakılan etiket içerikleri yansımaz. Örneğin `robots` değişkeni boş bırakıldığı için bu etiket html kodlarına geçmeyecektir.
```php
$meta = Seopack::Meta();

return view('frontend.index.index', compact('meta'));
```

Yapılacaklar
----

* Robots.txt dosyası için adres/klasör engelleme işlevi
* Yapısal veri (Structured Data) işlevi

Lisans
----

Bu yazılım paketi MIT lisansı ile lisanslanmıştır.

Destek
--------

Bu proje eğer işinize yaradıysa kripto paralarla bana bağışta bulunabilirsiniz. Aşağıda cüzdan adreslerimi bulabilirsiniz:

BTC: 397XEmzX2vXM7tLSdz1ZctkBUpn4Kio3ak

ETH: 0xbe3bd27670b2896088269050a5d0f19e801ee6c6

Tether / OMNİ: 1KHW9er1e7b4UoYCZWWet7QkNN4wwes84g
