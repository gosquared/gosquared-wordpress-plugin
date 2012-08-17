<?php

//  Add the live counter
    function live_visitors() {
        //  Add our script
        echo '<script>'; include_once DIR . 'assets/live.js.php'; echo '</script>';
        
        //  And add the element that spits it out
        echo '<span id="live-visitors"><b>1</b> people online</span>';
    }
    
//  Get the most popular pages
    function top_content($noScript = false, $blogOnly = true) {
        $url = 'https://api.gosquared.com/pages.json?api_key=' . get_option('gs_api') . '&site_token=' . get_option('gs_acct');
        $json = json_decode(file_get_contents($url));

        if(!$json->pages) {
            return;
        }
        
        $json = $json->pages;
        unset($json->cardinality);
        
        $target = array();

        if($blogOnly) {
            foreach($json as $url => $data) {
            
                $base = url_path(squared_url());
                
                if(strpos($url, $base) !== false and strpos($url, 'wp-admin') === false) {
                    $target[$url] = $data;
                }
            }
        }
        
        $json = (object) $target;
        
        if(count((array) $json) > 5) {
            $json = (object) array_slice((array) $json, 0, 5);
        }
        
        //  And echo it out
        echo '<div class="top-content-widget">';
        
        //  This should never happen (since you're visiting the page), but just in case...
        if(!$json) {
            echo '<span class="no-content">No content to show.</span></div>';
            return;
        }
        
        echo '<ul class="top-content">';
        echo '<li class="heading"><h2>Popular posts</h2></li>';
        
        foreach($json as $url => $data) {
            echo '<li>';
                echo '<a href="' . $url . '" title="' . $data->title . '">
                    <b>' . str_replace(get_bloginfo('sitename'), '', str_replace(' — ', '', $data->title)) . '</b>
                    <span class="url">' . str_replace(squared_url(), '', $url) . '</span>
                    
                    <span class="count">' . $data->visitors . '</span>
                </a>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '<small><a href="http://gosquared.com">Powered by GoSquared</a></small>';
        echo '<div>';
        
        if(!$noScript) {
            echo '<script>'; include_once DIR . 'assets/content.js'; echo '</script>';
        }
    }
    
    function squared_url() {
        return preg_replace('/(.*)(:[0-9]+?)\/(.*)/', '\\1/\\3', get_bloginfo('url'));
    }
    
    function url_path($url) {
        return parse_url($url, PHP_URL_PATH);
    }
    
//  show the top content widget
    if(isset($_GET['top_content'])) {
        top_content(true); exit;
    }