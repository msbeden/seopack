<?php

namespace msbeden\Seopack;

/**
 * Laravel 8 Seopack
 * @license MIT License
 * @author Mehmet Şaban BEDEN <msbeden@gmail.com>
 * @link https://www.msbeden.tk
 */

class Seopack
{
    public static $classVersion = '1.0';
    public static $sitemapFileName = "sitemap.xml";
    public static $sitemapIndexFileName = "sitemap-index.xml";
    public static $robotsFileName = "robots.txt";
    public static $maxURLsPerSitemap = 50000;
    public static $createGZipFile = false;
    private static $config;
    private static $baseURL;
    private static $basePath;
    private static $searchEngines = array(
        "http://www.google.com/webmasters/tools/ping?sitemap=",
        "http://submissions.ask.com/ping?sitemap=",
        "http://www.bing.com/webmaster/ping.aspx?siteMap="
    );
    private static $urls;
    private static $sitemaps;
    private static $sitemapIndex;
    private static $sitemapFullURL;

    public static function Meta(array $meta = null)
    {
        $valid_tags = ['title', 'description', 'keywords', 'author', 'publisher', 'robots'];
        $meta_tags  = null;
        if(!empty($meta)) {
            foreach ($meta as $key => $tag)
            {
                if(in_array($key, $valid_tags) AND !empty($tag)) {
                    if($key == 'title') {
                        $title = '<title>'.$tag.'</title>';
                        $meta_tags = $meta_tags.$title."\n";
                    } else {
                        $other = '<meta name="'.$key.'" content="'.$tag.'" />';
                        $meta_tags = $meta_tags.$other."\n";
                    }
                }
            }
        } else {
            if(app()['config']['seopack']['standart_meta_acik']) {
                foreach (app()['config']['seopack']['meta'] as $key => $tag)
                {
                    if(in_array($key, $valid_tags) AND !empty($tag)) {
                        if($key == 'title') {
                            $title = '<title>'.$tag.'</title>';
                            $meta_tags = $meta_tags.$title."\n";
                        } else {
                            $other = '<meta name="'.$key.'" content="'.$tag.'" />';
                            $meta_tags = $meta_tags.$other."\n";
                        }
                    }
                }
            }
        }

        return $meta_tags;
    }

    public static function OpenGraph(array $og = null)
    {
        $valid_tags = ['app_id', 'type', 'site_name', 'title', 'description', 'url', 'image', 'image:width', 'image:height', 'published_time', 'author'];
        $og_tags    = null;
        if(!empty($og)) {
            foreach ($og as $key => $tag)
            {
                if (in_array($key, $valid_tags) AND !empty($tag)) {
                    if ($key == 'app_id') {
                        $prefix = 'fb';
                    } elseif (($key == 'published_time') or ($key == 'author')) {
                        $prefix = 'article';
                    } else {
                        $prefix = 'og';
                    }

                    $variable = '<meta property="' . $prefix . ':' . $key . '" content="' . $tag . '" />' . "\n";
                    $og_tags = $og_tags . $variable;
                }
            }
        } else {
            if(app()['config']['seopack']['standart_opengraph_acik']) {
                foreach (app()['config']['seopack']['opengraph'] as $key => $tag)
                {
                    if (in_array($key, $valid_tags) AND !empty($tag)) {
                        if ($key == 'app_id') {
                            $prefix = 'fb';
                        } elseif (($key == 'published_time') or ($key == 'author')) {
                            $prefix = 'article';
                        } else {
                            $prefix = 'og';
                        }

                        $variable = '<meta property="' . $prefix . ':' . $key . '" content="' . $tag . '" />' . "\n";
                        $og_tags = $og_tags . $variable;
                    }
                }
            }
        }
        return $og_tags;
    }

    public static function TwitterCard(array $tw = null)
    {
        $valid_tags = ['site', 'title', 'description', 'image'];
        $prefix     = null;
        $tw_tags    = null;
        if(!empty($tw)) {
            $tw_tags    = '<meta name="twitter:card" content="summary_large_image">'."\n";
            foreach ($tw as $key => $tag)
            {
                if (in_array($key, $valid_tags) AND !empty($tag)) {
                    $prefix = ($key == 'site') ? '@' : null;

                    $variable = '<meta name="twitter:' . $key . '" content="' . $prefix . $tag . '" />' . "\n";
                    $tw_tags = $tw_tags . $variable;
                }
            }
        } else {
            $tw_tags    = '<meta name="twitter:card" content="summary_large_image">'."\n";
            if(app()['config']['seopack']['standart_twittercard_acik']) {
                foreach (app()['config']['seopack']['twittercard'] as $key => $tag)
                {
                    if (in_array($key, $valid_tags) AND !empty($tag)) {
                        $prefix = ($key == 'site') ? '@' : null;

                        $variable = '<meta name="twitter:' . $key . '" content="' . $prefix . $tag . '" />' . "\n";
                        $tw_tags = $tw_tags . $variable;
                    }
                }
            }
        }
        return $tw_tags;
    }

    public static function SitemapRoot($baseURL, $basePath = "") {
        self::$baseURL = $baseURL;
        self::$basePath = $basePath;
    }

    public static function SitemapUrls($urlsArray) {
        if (!is_array($urlsArray))
            throw new InvalidArgumentException("Argüman olarak dizi verilmelidir.");
        foreach ($urlsArray as $url) {
            self::SitemapUrl(isset ($url[0]) ? $url[0] : null,
                isset ($url[1]) ? $url[1] : null,
                isset ($url[2]) ? $url[2] : null,
                isset ($url[3]) ? $url[3] : null);
        }
    }

    public static function SitemapUrl($url, $lastModified = null, $changeFrequency = null, $priority = null) {
        if ($url == null)
            throw new InvalidArgumentException("URL zorunludur. En az bir argüman verilmelidir.");
        $urlLenght = extension_loaded('mbstring') ? mb_strlen($url) : strlen($url);
        if ($urlLenght > 2048)
            throw new InvalidArgumentException("URL uzunluğu 2048 karakterden büyük olamaz.
                                                Unutmayın, kesin URL uzunluk kontrolü sadece mb_string uzantısı kullanılarak yapılmalıdır.
                                                Sunucunuzun mbstring uzantısını kullanmasına izin verdiğinden emin olun.");
        $tmp = array();
        $tmp['loc'] = $url;
        if (isset($lastModified)) $tmp['lastmod'] = $lastModified;
        if (isset($changeFrequency)) $tmp['changefreq'] = $changeFrequency;
        if (isset($priority)) $tmp['priority'] = $priority;
        self::$urls[] = $tmp;
    }

    public static function SitemapGenerate() {
        if (!isset(self::$urls))
            echo "Site haritası oluşturmak için önce SitemapUrl veya SitemapUrls işlevini çağırın.";
        if (self::$maxURLsPerSitemap > 50000)
            echo "Her bir site haritası için 50.000'den fazla URL'ye izin verilmez.";

        $generatorInfo = '<!-- generated-on="'.date('c').'" --><!-- sitemap-generator-url="http://www.msbeden.tk" sitemap-generator-version="'.self::$classVersion.'" -->';
        $sitemapHeader = '<?xml version="1.0" encoding="UTF-8"?>'.$generatorInfo.'
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach(array_chunk(self::$urls,self::$maxURLsPerSitemap) as $sitemap) {
            $xml = '';
            $xml = $xml.$sitemapHeader;

            foreach ($sitemap as $url) {
                $xml = $xml. '
    <url>
        <loc>'.$url['loc'].'</loc>';
                if (isset($url['lastmod']))
                    $xml = $xml. '
        <lastmod>'.$url['lastmod'].'</lastmod>';
                if (isset($url['changefreq']))
                    $xml = $xml. '
        <changefreq>'.$url['changefreq'].'</changefreq>';
                if (isset($url['priority']))
                    $xml = $xml. '
        <priority>'.$url['priority'].'</priority>';
                $xml = $xml. '
    </url>';
            }
            $xml = $xml. '
</urlset>';

            if (strlen($xml) > 10485760)
                echo "Site haritası boyutu 10 MB’dan büyük (10,485,760), lütfen maxURLsPerSitemap değişkenini azaltın.";

            self::$sitemaps[] = $xml;
        }

        if (sizeof(self::$sitemaps) > 1000)
            echo "Site Haritası dizini, 1000 tek site haritası içerebilir. Belki de çok fazla URL göndermeye çalışıyorsunuzdur.";

    }

    public static function SitemapPrint() {
        if (!isset(self::$sitemaps))
            echo "Site haritası yazmak için önce SitemapGenerate işlevini çağırın.";

        if (isset(self::$sitemapIndex)) {
            self::_writeFile(self::$sitemapIndex[1], self::$basePath, self::$sitemapIndex[0]);
            foreach(self::$sitemaps as $sitemap) {
                self::_writeGZipFile($sitemap[1], self::$basePath, $sitemap[0]);
            }
        }
        else {
            self::_writeFile(self::$sitemaps[0], self::$basePath, self::$sitemapFileName);
            if (self::$createGZipFile)
                self::_writeGZipFile(self::$sitemaps[0], self::$basePath, self::$sitemapFileName.".gz");
        }
    }

    private static function _writeFile($content, $filePath, $fileName) {
        $file = fopen($filePath.$fileName, 'w');
        fwrite($file, $content);
        return fclose($file);
    }

    private static function _writeGZipFile($content, $filePath, $fileName) {
        $file = gzopen($filePath.$fileName, 'w');
        gzwrite($file, $content);
        return gzclose($file);
    }

    public static function SitemapRobotsUpdate() {
        if (!isset(self::$sitemaps))
            echo "Robots.txt dosyasını güncellemek için önce SitemapGenerate işlevini çağırın.";
        $sampleRobotsFile = "User-agent: *\nAllow: /";
        self::$sitemapFullURL   = self::$baseURL.self::$sitemapFileName;
        if (file_exists(self::$basePath.self::$robotsFileName)) {
            $robotsFile = explode("\n", file_get_contents(self::$basePath.self::$robotsFileName));
            $robotsFileContent = "";
            foreach($robotsFile as $key=>$value) {
                if(substr($value, 0, 8) == 'Sitemap:') unset($robotsFile[$key]);
                else $robotsFileContent .= $value."\n";
            }
            $robotsFileContent .= "Sitemap: ".self::$sitemapFullURL;
            if (self::$createGZipFile && !isset(self::$sitemapIndex))
                $robotsFileContent .= "\nSitemap: ".self::$sitemapFullURL.".gz";
            file_put_contents(self::$basePath.self::$robotsFileName,$robotsFileContent);
        } else {
            $sampleRobotsFile = $sampleRobotsFile."\n\nSitemap: ".self::$sitemapFullURL;
            if (self::$createGZipFile && !isset(self::$sitemapIndex))
                $sampleRobotsFile .= "\nSitemap: ".self::$sitemapFullURL.".gz";
            file_put_contents(self::$basePath.self::$robotsFileName, $sampleRobotsFile);
        }
    }
}
