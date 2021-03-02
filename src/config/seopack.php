<?php

/**
 * Laravel 8 Seopack
 * @license MIT License
 * @author Mehmet Şaban BEDEN <msbeden@gmail.com>
 * @link https://www.msbeden.tk
 */

return [

    // Genel Ayarlar
    'standart_meta_acik'        => true,
    'standart_opengraph_acik'   => false,
    'standart_twittercard_acik' => false,

    //Meta Tags
    'meta' => [
        'title'         => 'Test Başlık',
        'description'   => 'Test Açıklama',
        'keywords'      => 'Test, Keywords',
        'author'        => 'Test Author',
        'publisher'     => 'Test Publisher',
        'robots'        => ''
    ],

    //OpenGraph Tags
    'opengraph' => [
        'app_id'        => '',
        'type'          => '',
        'site_name'     => '',
        'title'         => '',
        'description'   => '',
        'url'           => '',
        'image'         => '',
        'image:width'   => '',
        'image:height'  => '',
        'published_time'=> '',
        'author'        => '',
    ],

    //TwitterCard Tags
    'twittercard' => [
        'site'          => '',
        'title'         => '',
        'description'   => '',
        'image'         => ''
    ],

];
