<?php
/*
Plugin Name: GoSquared Wordpress Plugin
Plugin URI: http://www.gosquared.com/
Description: The official GoSquared Wordpress plugin to load the Tracking Code for GoSquared applications on your Wordress-powered site
Version: 0.3.3
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
    $trackUser = get_option('gstc_trackUser');
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

/* * **********************************************************************
 *  Widgets
 * --------------------------------- 
 * Each widget extends WP_Widget:

  class My_Widget extends WP_Widget {
  function My_Widget() {
  // widget actual processes
  }
  function form($instance) {
  // outputs the options form on admin
  }

  function update($new_instance, $old_instance) {
  // processes widget options to be saved
  }

  function widget($args, $instance) {
  // outputs the content of the widget
  }
  }

 *
 * Must be registered in load_gs_widgets:
 * register_widget('My_Widget');
 * ********************************************************************** */

if (class_exists("WP_Widget")) {

// widgets go here
add_action('wp_enqueue_scripts', 'gs_enqueue_scripts');
add_action('widgets_init', 'load_widget');

function gs_enqueue_scripts()
{
    wp_enqueue_script('jquery');
}

function load_widget()
{
    register_widget('WP_Widget_GS_OnlineVisitors');
}

class WP_Widget_GS_OnlineVisitors extends WP_Widget
{

    function WP_Widget_GS_OnlineVisitors()
    {
        // widget actual processes
        $widget_ops = array('classname' => 'widget_gs_onlinevisitors', 'description' => __('Display the number of visitors currently on your site'));
        $control_ops = array('height' => 320, 'width' => '600', 'id_base' => 'gs_onlinevisitors');
        parent::__construct('gs_onlinevisitors', __('GoSquared Online Visitors'), $widget_ops, $control_ops);
    }

    function form($instance)
    {
        // set default options
        $defaults = array('style' => 1);
        // outputs the options form on admin
        $instance = wp_parse_args((array)$instance, $defaults);
        ?>
    <h2>GoSquared</h2>

    <h3>Choose the style for this widget</h3>
    <?php $wstyle = $instance['style']; ?>

    <style type="text/css">
        .gs-widget-label .gs-img-container {
            display: inline-block;
            width: 180px;
            height: 180px;
            position: relative;
            text-align: center;
        }

        .gs-widget-label {
            display: inline-block;
            text-align: center;
        }

        .gs-widget-label .gs-img-container {
            display: block;
        }
    </style>
    <label for="gs-widget-option-1" class="gs-widget-label">
        <div class="gs-img-container"><img
            src="<?php echo WP_PLUGIN_URL; echo '/' . GS_PLUGIN_DIR;?>/wordpress_plugin_01_150x160.png" align="top"
            alt="GoSquared Widget 01"/></div>
        <input type="radio" id="gs-widget-option-1" name="<?php echo $this->get_field_name('style'); ?>"
            <?php if (1 == $wstyle)
            echo 'checked'; ?> value="1"/>
    </label>

    <label for="gs-widget-option-2" class="gs-widget-label">
        <div class="gs-img-container"><img
            src="<?php echo WP_PLUGIN_URL; echo '/' . GS_PLUGIN_DIR;?>/wordpress_plugin_02_150x160.png" align="top"
            alt="GoSquared Widget 02"/></div>
        <input type="radio" id="gs-widget-option-2" name="<?php echo $this->get_field_name('style'); ?>"
            <?php if (2 == $wstyle)
            echo 'checked'; ?> value="2"/>
    </label>

    <label for="gs-widget-option-3" class="gs-widget-label">
        <div class="gs-img-container"><img
            src="<?php echo WP_PLUGIN_URL . '/' . GS_PLUGIN_DIR; ?>/wordpress_plugin_03_170x160.png" align="top"
            alt="GoSquared Widget 03"/></div>
        <input type="radio" id="gs-widget-option-3" name="<?php echo $this->get_field_name('style'); ?>"
            <?php if (3 == $wstyle)
            echo 'checked'; ?> value="3"/>
    </label>

    <?php
    }

    function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
        $instance = $old_instance;
        $instance['style'] = $new_instance['style'];
        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        $cache_timeout = get_option('gstc_cacheTimeout');
        if (!$cache_timeout) $cache_timeout = DEFAULT_CACHE_TIMEOUT;
        if ($title)
            echo $before_title . $title . $after_title;
        // set local script settings
        ?>
    <script type="text/javascript">
        var plugin_proxy_url = '<?php
            $gs_plugin_url = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__));
            echo "$gs_plugin_url/apiproxy.php?widget=";
            ?>';
        var cache_timeout = (+'<?php echo $cache_timeout ?>' || 30) * 1000; // seconds -> milliseconds
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script><?php
        $widget_url = WP_PLUGIN_DIR . '/' . GS_PLUGIN_DIR . "/widgets/gs-onlinevisitors/" . $instance['style'] . "/gs-onlinevisitors.html";
        echo file_get_contents($widget_url);
        echo $after_widget;
    }

}

} // end if (class_exists(..))

include 'GS_GetEvents.php';
?>
