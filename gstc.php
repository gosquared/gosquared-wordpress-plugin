<?php
/*
Plugin Name: GoSquared Wordpress Plugin
Plugin URI: http://www.gosquared.com/
Description: The official GoSquared Wordpress plugin to load the Tracking Code for GoSquared applications on your Wordress-powered site
Version: 0.1.0
License: GPL3 http://www.gnu.org/licenses/gpl.html
Author: GoSquared
Author URI: http://www.gosquared.com/about/
Contributions by: Jack Kingston, Aaran Parker
*/

/*  Copyright 2012 GoSquared (email : support@gosquared.com)

    This file is part of GoSquared Wordpress Plugin.

    GoSquared Wordpress Plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GoSquared Wordpress Plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GoSquared WooCommerce Plugin.  If not, see <http://www.gnu.org/licenses/>.
*/
define('GS_PLUGIN_DIR', basename(dirname(__FILE__)));
define('GS_API_CACHE_DIR', WP_PLUGIN_DIR . '/' . GS_PLUGIN_DIR . '/apicache');

include 'gsadmin.php';
include 'apiproxy.php';

add_action('init', 'gs_init');
add_action('admin_footer', 'gs_print_gstc');
add_action('wp_footer', 'gs_print_gstc');
add_action('login_footer', 'gs_print_gstc');

function gs_init() {
    $style_url = WP_PLUGIN_URL . '/' . GS_PLUGIN_DIR . '/gs.css';
    $style_file = WP_PLUGIN_DIR . '/' . GS_PLUGIN_DIR .'/gs.css';
    /* Register our stylesheet. */
    wp_register_style('gs_style', $style_url);
}

function gs_print_gstc() {
    $acct = get_option('gstc_acct');
    $trackAdmin = get_option('gstc_trackAdmin');
    $trackPreview = get_option('gstc_trackPreview');
    //Check if we are not tracking admin pages and if this is an admin page then return
    $isAdminAndNotTracking = is_admin() && $trackAdmin == 'No';
    $isPreviewAndNotTracking = isset($_GET['preview']) && $_GET['preview'] == 'true' 
                                && $trackPreview == 'No';
    if ($acct && !$isAdminAndNotTracking && !$isPreviewAndNotTracking) {
        print_tracker_script();
    }
}

function print_tracker_script() {
    $acct = get_option('gstc_acct');
    $params = array();
    $params['acct'] = $acct;
    $gstc_userDetail = get_username_or_default();
    if ($gstc_userDetail) {
        $params['VisitorName'] = $gstc_userDetail;
    }
    include 'tracker_template.php';
}

function print_visitor_params() {
    $params = array();
    $acct = get_option('gstc_acct');
    $params['acct'] = $acct;
    $gstc_userDetail = get_username_or_default();
    if ($gstc_userDetail) {
        $params['VisitorName'] = $gstc_userDetail;
    }
}

function get_username_or_default() {
    $gstc_userDetail = '';
    $trackUser = get_option('gstc_trackUser');
    //If tracking names, get the relevant information
    if ($trackUser != 'Off') {
        //Get current user
        require_once(ABSPATH . WPINC . "/pluggable.php");
        $current_user = wp_get_current_user();
        //Check if current user is not a guest
        if (user_is_not_a_guest($current_user)) {
            $gstc_userDetail = get_appropriate_user_detail($current_user);
        }
    }
    return $gstc_userDetail;
}

function user_is_not_a_guest($user) {
    return 0 != $user->ID;
}

function get_appropriate_user_detail($current_user) {
    $gstc_userDetail = '';
    switch ($trackUser) {
        case 'UserID':
            $gstc_userDetail = $current_user->ID;
            break;

        case 'Username':
            $gstc_userDetail = $current_user->user_login;
            break;

        case 'DisplayName':
            $gstc_userDetail = $current_user->display_name;
            break;

        default:
            $gstc_userDetail = false;
            break;
    }
    return $gstc_userDetail;
}

include 'GS_GetEvents.php';
?>
