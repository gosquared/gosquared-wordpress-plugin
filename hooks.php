<?php

//  Set some options
    $gs_options = array(
        'gs_acct' => 'Your GoSquared Site Token. Get it from your <a href="https://www.gosquared.com/home/developer">developer settings</a>.',
        'gs_api'  => 'Your GoSquared API Key. Get it from your <a href="https://www.gosquared.com/home/developer">developer settings</a>.'
    );
    
//  Placeholders
    $placeholders = array('gs_acct' => 'GSN-000000-Z', 'gs_api' => 'XXXXXXXXXXXXXXXX');
    
//  Loop the options and add to WP
    foreach($gs_options as $key => $value) {
        add_option($key);
    }
    
//  Add some actions to the main site
    add_action('admin_menu', 'options_menu');
    add_action('wp_footer', 'tracking_code');

//  Add some functions to handle this shit
//  Add the options menu
    function options_menu() {
        return add_options_page('GoSquared', 'GoSquared', 'manage_options', 'gosquared', 'options_page');
    }
    
//  And the page
    function options_page() {
        if(!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    
        global $gs_options, $placeholders, $post, $errors;
        
        $path = DIR . 'assets/options.php';
        
        //  Add the tracking code
        tracking_code();
        
        //  Spit the stylesheet out
        echo '<style>';
            ob_start();
            include_once DIR . 'assets/style.css';
            $content = ob_get_clean();
            
            echo str_replace('{dir}', substr(plugin_dir_url(__FILE__), 0, -1), $content);
        echo '</style>';
        
        //  Handle post
        if(isset($_POST['submit'])) {
            handle_post();
        }
        
        if(file_exists($path)) {
            include_once $path;
        }
        
        return;
    }
    
//  Check if a user has the tracking code
    function has_tracking_code() {
        return get_option('gs_acct') !== '' && get_option('gs_api') !== '';
    }
    
//  Add the tracking code in
    function tracking_code() {
        if(has_tracking_code() and file_exists(DIR . 'assets/code.php')) {
            $c = '{acct: "' . get_option('gs_acct') . '"}';
            
            include_once DIR . 'assets/code.php';
        }
    }
    
//  Handle the post updating
    function handle_post() {
        global $gs_options, $post, $errors;
        $post = array();
        $errors = array();
        
        foreach($gs_options as $key => $discard) {
            $post[$key] = strip_tags(filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING));
        }
        
        //  Validate site tokens
        $acct = isset($post['gs_acct']) ? strtoupper($post['gs_acct']) : '';
        if(!empty($acct) and !preg_match('/^GSN-[0-9]+-[A-Z]$/', $acct)) { 
            $errors['gs_acct'] = 'Invalid site token. Get it from your <a href="https://www.gosquared.com/home/developer">developer settings</a>.';
        }
        
        //  Validate the API key
        $api = isset($post['gs_api']) ? strtoupper($post['gs_api']) : '';
        if(!empty($acct) and !preg_match('/^[A-Z0-9]{16}$/', $api)) { 
            $errors['gs_api'] = 'Invalid API key. Get it from your <a href="https://www.gosquared.com/home/developer">developer settings</a>.';
        }
        
        //  If there's no errors, go nuts and add it to the DB
        if(empty($errors)) {
            foreach($gs_options as $key => $discard) {
                update_option($key, $post[$key]);
            }
        }
    }