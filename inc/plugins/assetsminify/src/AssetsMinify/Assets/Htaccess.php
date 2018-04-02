<?php
namespace AssetsMinify\Assets;

/**
 * Css Factory.
 * Manages the styles (Css and sass, scss, stylus, less)
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Htaccess {

    protected $assets = array(),
              $files  = array(),
              $mtimes = array();

    public function is_subdirectory_install(){
        if(strlen(site_url()) > strlen(home_url())){
            return true;
        }
        return false;
    }

    public function getABSPATH(){
        $path = ABSPATH;
        $siteUrl = site_url();
        $homeUrl = home_url();
        $diff = str_replace($homeUrl, "", $siteUrl);
        $diff = trim($diff,"/");

        $pos = strrpos($path, $diff);

        if($pos !== false){
            $path = substr_replace($path, "", $pos, strlen($diff));
            $path = trim($path,"/");
            $path = "/".$path."/";
        }
        return $path;
    }

    public function modifyHtaccess(){

            $path = ABSPATH;
            if($this->is_subdirectory_install()){
                $path = $this->getABSPATH();
            }

            if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"])){
                return array("The plugin does not work with Microsoft IIS. Only with Apache", "error");
            }

            // if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
            //  return array("The plugin does not work with Nginx. Only with Apache", "error");
            // }

            if(!file_exists($path.".htaccess")){
                if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
                    //
                }else{
                    return array("<label>.htaccess was not found</label> <a target='_blank' href='http://www.wpfastestcache.com/warnings/htaccess-was-not-found/'>Read More</a>", "error");
                }
            }

            if($this->isPluginActive('wp-postviews/wp-postviews.php')){
                $wp_postviews_options = get_option("views_options");
                $wp_postviews_options["use_ajax"] = true;
                update_option("views_options", $wp_postviews_options);

                if(!WP_CACHE){
                    if($wp_config = @file_get_contents(ABSPATH."wp-config.php")){
                        $wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);

                        if(!@file_put_contents(ABSPATH."wp-config.php", $wp_config)){
                            return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
                        }
                    }else{
                        return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
                    }
                }
            }

            $htaccess = @file_get_contents($path.".htaccess");

            // if(defined('DONOTCACHEPAGE')){
            //  return array("DONOTCACHEPAGE <label>constant is defined as TRUE. It must be FALSE</label>", "error");
            // }else

            if(!get_option('permalink_structure')){
                return array("You have to set <strong><u><a href='".admin_url()."options-permalink.php"."'>permalinks</a></u></strong>", "error");
            }else if($res = $this->checkSuperCache($path, $htaccess)){
                return $res;
            }else if($this->isPluginActive('far-future-expiration/far-future-expiration.php')){
                return array("Far Future Expiration Plugin", "error");
            }else if($this->isPluginActive('fast-velocity-minify/fvm.php')){
                return array("Fast Velocity Minify needs to be deactived", "error");
            }else if($this->isPluginActive('sg-cachepress/sg-cachepress.php')){
                return array("SG Optimizer needs to be deactived", "error");
            }else if($this->isPluginActive('adrotate/adrotate.php') || $this->isPluginActive('adrotate-pro/adrotate.php')){
                return $this->warningIncompatible("AdRotate");
            }else if($this->isPluginActive('mobilepress/mobilepress.php')){
                return $this->warningIncompatible("MobilePress", array("name" => "WPtouch Mobile", "url" => "https://wordpress.org/plugins/wptouch/"));
            }else if($this->isPluginActive('speed-booster-pack/speed-booster-pack.php')){
                return array("Speed Booster Pack needs to be deactivated<br>", "error");
            }else if($this->isPluginActive('cdn-enabler/cdn-enabler.php')){
                return array("CDN Enabler needs to be deactivated<br>This plugin has aldready CDN feature", "error");
            }else if($this->isPluginActive('wp-performance-score-booster/wp-performance-score-booster.php')){
                return array("WP Performance Score Booster needs to be deactivated<br>This plugin has aldready Gzip, Leverage Browser Caching features", "error");
            }else if($this->isPluginActive('bwp-minify/bwp-minify.php')){
                return array("Better WordPress Minify needs to be deactivated<br>This plugin has aldready Minify feature", "error");
            }else if($this->isPluginActive('check-and-enable-gzip-compression/richards-toolbox.php')){
                return array("Check and Enable GZIP compression needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
            }else if($this->isPluginActive('gzippy/gzippy.php')){
                return array("GZippy needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
            }else if($this->isPluginActive('gzip-ninja-speed-compression/gzip-ninja-speed.php')){
                return array("GZip Ninja Speed Compression needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
            }else if($this->isPluginActive('wordpress-gzip-compression/ezgz.php')){
                return array("WordPress Gzip Compression needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
            }else if($this->isPluginActive('filosofo-gzip-compression/filosofo-gzip-compression.php')){
                return array("GZIP Output needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
            }else if($this->isPluginActive('head-cleaner/head-cleaner.php')){
                return array("Head Cleaner needs to be deactivated", "error");
            }else if($this->isPluginActive('far-future-expiry-header/far-future-expiration.php')){
                return array("Far Future Expiration Plugin needs to be deactivated", "error");
            }else if(is_writable($path.".htaccess")){
                // $htaccess = $this->insertWebp($htaccess);

                $htaccess = $this->insertLBCRule($htaccess);
                $htaccess = $this->insertGzipRule($htaccess);
                // $htaccess = $this->insertRewriteRule($htaccess, $post);
                //$htaccess = preg_replace("/\n+/","\n", $htaccess);

                file_put_contents($path.".htaccess", $htaccess);
            }else{
                //return array("Options have been saved", "success");
                return array(".htaccess is not writable", "error");
            }
            return array("Options have been saved", "success");

        }

    public function insertLBCRule($htaccess){

        if(get_option('am_leverage_browser_caching', 1) == 1){

            $data = "# BEGIN LBCAssetMinify"."\n".
                    '<FilesMatch "\.(ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|ttf|svg|eot)(\.gz)?$">'."\n".
                    '<IfModule mod_expires.c>'."\n".
                    'AddType application/font-woff2 .woff2'."\n".
                    'ExpiresActive On'."\n".
                    'ExpiresDefault A0'."\n".
                    'ExpiresByType image/webp A2592000'."\n".
                    'ExpiresByType image/gif A2592000'."\n".
                    'ExpiresByType image/png A2592000'."\n".
                    'ExpiresByType image/jpg A2592000'."\n".
                    'ExpiresByType image/jpeg A2592000'."\n".
                    'ExpiresByType image/ico A2592000'."\n".
                    'ExpiresByType image/svg+xml A2592000'."\n".
                    'ExpiresByType text/css A2592000'."\n".
                    'ExpiresByType text/javascript A2592000'."\n".
                    'ExpiresByType application/javascript A2592000'."\n".
                    'ExpiresByType application/x-javascript A2592000'."\n".
                    'ExpiresByType application/font-woff2 A2592000'."\n".
                    '</IfModule>'."\n".
                    '<IfModule mod_headers.c>'."\n".
                    'Header set Expires "max-age=2592000, public"'."\n".
                    'Header unset ETag'."\n".
                    'Header set Connection keep-alive'."\n".
                    'FileETag None'."\n".
                    '</IfModule>'."\n".
                    '</FilesMatch>'."\n".
                    "# END LBCAssetMinify"."\n";

                if(!preg_match("/BEGIN\s*LBCAssetMinify/", $htaccess)){
                    return $data.$htaccess;
                }else{
                    return $htaccess;
                }
        }else{
            //delete leverage browser caching
            $htaccess = preg_replace("/#\s?BEGIN\s?LBCAssetMinify.*?#\s?END\s?LBCAssetMinify/s", "", $htaccess);
            return $htaccess;
        }
    }

    public function insertGzipRule($htaccess){
        if(get_option('am_enable_compression', 1) == 1){
            $data = "# BEGIN GzipAssetMinify"."\n".
                    "<IfModule mod_deflate.c>"."\n".
                    "AddType x-font/woff .woff"."\n".
                    "AddType x-font/ttf .ttf"."\n".
                    "AddOutputFilterByType DEFLATE image/svg+xml"."\n".
                    "AddOutputFilterByType DEFLATE text/plain"."\n".
                    "AddOutputFilterByType DEFLATE text/html"."\n".
                    "AddOutputFilterByType DEFLATE text/xml"."\n".
                    "AddOutputFilterByType DEFLATE text/css"."\n".
                    "AddOutputFilterByType DEFLATE text/javascript"."\n".
                    "AddOutputFilterByType DEFLATE application/xml"."\n".
                    "AddOutputFilterByType DEFLATE application/xhtml+xml"."\n".
                    "AddOutputFilterByType DEFLATE application/rss+xml"."\n".
                    "AddOutputFilterByType DEFLATE application/javascript"."\n".
                    "AddOutputFilterByType DEFLATE application/x-javascript"."\n".
                    "AddOutputFilterByType DEFLATE application/x-font-ttf"."\n".
                    "AddOutputFilterByType DEFLATE application/vnd.ms-fontobject"."\n".
                    "AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf"."\n".
                    "</IfModule>"."\n";

            if(defined("WPFC_GZIP_FOR_COMBINED_FILES") && WPFC_GZIP_FOR_COMBINED_FILES){
                $data = $data."\n".'<FilesMatch "\d+index\.(css|js)(\.gz)?$">'."\n".
                        "# to zip the combined css and js files"."\n\n".
                        "RewriteEngine On"."\n".
                        "RewriteCond %{HTTP:Accept-encoding} gzip"."\n".
                        "RewriteCond %{REQUEST_FILENAME}\.gz -s"."\n".
                        "RewriteRule ^(.*)\.(css|js) $1\.$2\.gz [QSA]"."\n\n".
                        "# to revent double gzip and give the correct mime-type"."\n\n".
                        "RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1,E=FORCE_GZIP]"."\n".
                        "RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1,E=FORCE_GZIP]"."\n".
                        "Header set Content-Encoding gzip env=FORCE_GZIP"."\n".
                        "</FilesMatch>"."\n";
            }

            $data = $data."# END GzipAssetMinify"."\n";

            $htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);
            return $data.$htaccess;

        }else{
            //delete gzip rules
            $htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);
            return $htaccess;
        }
    }

    public function checkSuperCache($path, $htaccess){
        if($this->isPluginActive('wp-super-cache/wp-cache.php')){
            return array("WP Super Cache needs to be deactive", "error");
        }else{
            @unlink($path."wp-content/wp-cache-config.php");

            $message = "";

            if(is_file($path."wp-content/wp-cache-config.php")){
                $message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
            }

            if(preg_match("/supercache/", $htaccess)){
                $message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
            }

            return $message ? array("WP Super Cache cannot remove its own remnants so please follow the steps below".$message, "error") : "";
        }

        return "";
    }
    public function isPluginActive( $plugin ) {
            return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->isPluginActiveForNetwork( $plugin );
        }

        public function isPluginActiveForNetwork( $plugin ) {
            if ( !is_multisite() )
                return false;

            $plugins = get_site_option( 'active_sitewide_plugins');
            if ( isset($plugins[$plugin]) )
                return true;

            return false;
        }

    public function warningIncompatible($incompatible, $alternative = false){
        if($alternative){
            return array($incompatible." <label>needs to be deactive</label><br><label>We advise</label> <a id='alternative-plugin' target='_blank' href='".$alternative["url"]."'>".$alternative["name"]."</a>", "error");
        }else{
            return array($incompatible." <label>needs to be deactive</label>", "error");
        }
    }
    /**
     * Prints <link> tag to include the CSS
     *
     * @param string $filename The filename to dump
     * @param string $media The media attribute - Default = all
     */
    protected function dump( $filename, $media = 'all' ) {
        echo '<noscript id="wf_ds"><link rel="stylesheet" type="text/css" href="' . $this->cache->getUrl() . $filename . '"/></noscript>'."\r\n";
    }
}