<?php
/*
Plugin Name: GoSquared
Plugin URI: http://www.gosquared.com/
Description: The official GoSquared Wordpress plugin to load the Tracking Code for GoSquared applications
Version: 0.3.0
License: GPL3 http://www.gnu.org/licenses/gpl.html
Author: GoSquared
Author URI: http://www.gosquared.com/about/
Contributions by: Aaron Parker, Jack Kingston
 */

/*  Copyright 2011 GoSquared (email : support@gosquared.com)

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
    along with GoSquared Wordpress Plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

//ini_set('display_errors', "on");
add_option('gstc_acct');
add_option('gstc_trackAdmin');
add_option('gstc_trackPreview');
add_option('gstc_trackUser');
add_option('gstc_apiKey');
add_action('admin_init', 'gs_init');
add_action('admin_menu', 'gs_options');
gs_print_gstc();

define('MIN_TIMEOUT', 5);
define('DEFAULT_CACHE_TIMEOUT', 30);
define('GS_API_CACHE_DIR', WP_PLUGIN_DIR.'/gosquared-livestats/apicache');

function gs_init() {
    $style_url = WP_PLUGIN_URL . '/gosquared-livestats/gs.css';
    $style_file = WP_PLUGIN_DIR . '/gosquared-livestats/gs.css';
    /* Register our stylesheet. */
    wp_register_style('gs_style', $style_url);
}

//function gs_activate(){
//    //Get the $wpdb global
//   global $wpdb;
//   //Set a default result
//   $result = false;
//   //Install table, if it doesnt exist already
//   $sql = sprintf('CREATE TABLE IF NOT EXISTS `%sgs_meta` (
//      `meta_id` bigint(20) UNSIGNED NOT NULL auto_increment,
//      `meta_key` varchar(255),
//      `meta_value` longtext,
//      PRIMARY KEY (`meta_id`)
//   )',$wpdb->prefix);
//   $result = $wpdb->query($sql);
//}

function gs_options() {
    $page = add_options_page('GoSquared', 'GoSquared', 'manage_options', 'gs-livestats', 'gs_options_page');
    /* Using registered $page handle to hook stylesheet loading */
    add_action('admin_print_styles-' . $page, 'gs_admin_style');
}

function gs_admin_style() {
    wp_enqueue_style('gs_style');
}

function gs_success($message) {
    echo '<div class="center"><div class="message_wrapper"><div class="gs_success">' . $message . '</div></div></div>';
}

function gs_fail($message) {
    echo '<div class="center"><div class="message_wrapper"><div class="gs_fail">' . $message . '</div></div></div>';
}

function gs_warn($message) {
    echo '<div class="center"><div class="message_wrapper"><div class="gs_warn">' . $message . '</div></div></div>';
}

/*
  function is_tracker_installed() {
  $list_sites_raw = file_get_contents(WP_PLUGIN_URL.'/gosquared/apiproxy.php?widget=gs-tracking-check');
  $list_sites_obj = json_decode($list_sites_raw,true);
  $site_token = get_option("gstc_acct");
  $result = false;
  foreach ($list_sites_obj as $site) {
  if (strcmp($site['token'], $site_token) == 0) {
  if ($site["tracker_installed"] == 1)
  $result = true;
  }
  }
  return $result;
  }
 */

function gs_options_page() {
    global $style_file, $style_url;

    if (!current_user_can('manage_options')) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>

    <div id="gs-admin-settings-page">
        <br />
	
	
	<a href="http://www.gosquared.com/" title="Go to the GoSquared homepage" target="_blank"><div id="gosquaredlogo"></div></a>
	
	<?php
	if (isset($_POST['gs_acct'])) {
	    // Handle submission
	    $acct = $_POST['gs_acct'];
	    $apiKey = $_POST['gs_apiKey'];
	    $trackAdmin = isset($_POST['gs_trackAdmin']) ? $_POST['gs_trackAdmin'] : 'Yes';
	    $trackPreview = isset($_POST['gs_trackPreview']) ? $_POST['gs_trackPreview'] : "Yes";
	    $trackUser = isset($_POST['gs_trackUser']) ? $_POST['gs_trackUser'] : 'Username';
	    $cacheTimeout = isset($_POST['gs_cacheTimeout']) ? $_POST['gs_cacheTimeout'] : 30;
	    $valid_acct = preg_match('/^GSN-[0-9]{6,7}-[A-Z]{1}$/', $acct);
	    $valid_apiKey = preg_match('/^[0-9A-Z]{16}$/', $apiKey);
	    if ($valid_acct && $valid_apiKey) {
		update_option('gstc_acct', $acct);
		update_option('gstc_apiKey', $apiKey);
		update_option('gstc_trackAdmin', $trackAdmin);
		update_option('gstc_trackPreview', $trackPreview);
		update_option('gstc_trackUser', $trackUser);
		update_option('gstc_cacheTimeout', $cacheTimeout);
		gs_success('Settings updated successfully');
	    } else {
		$msg = "";
		if (!$valid_acct)
		    $msg .= '<p>Site token not of valid format. Must be like GSN-000000-X</p>';
		if (!$valid_apiKey)
		    $msg .= '<p>API key not of valid format. Must be a 16 characters long and only contains capital letters and numbers</p>';
		if(!$msg) $msg = 'An error occurred';
		gs_fail($msg);
	    }
	}

	$acct = get_option('gstc_acct');
	$apiKey = get_option('gstc_apiKey');
	$trackAdmin = get_option('gstc_trackAdmin');
	$trackPreview = get_option('gstc_trackPreview');
	$trackUser = get_option('gstc_trackUser');
	$cacheTimeout = get_option('gstc_cacheTimeout');
	?>

	<div class="gs-admin-header">

	    <?php
	    if (!$acct)
		$default_text = 'GSN-000000-X';
	    else
		$default_text = $acct;

	    $default_apiKey = "";
	    if (!$apiKey)
		$default_apiKey = '';
	    else
		$default_apiKey = $apiKey;

	    if (!$trackAdmin)
		$trackAdmin = 'Yes';

	    if (!$trackPreview)
		$trackPreview = 'Yes';

	    if (!$trackUser)
		$trackUser = 'Username';

	    if (!$cacheTimeout)
		$cacheTimeout = 30;
	    ?>

	    <h2>GoSquared Widgets - Share your stats with your audience.</h2>
	    <?php

	    $cachedir_help_link = '<a href = "http://www.gosquared.com/support/wiki/wordpress_plugin#cachedir" target="_blank">What does this mean?</a>';
	    $permissions_help = '<a href = "http://www.gosquared.com/support/wiki/wordpress_plugin#cachedir-permissions" target="_blank">More information</a>';
	    //$dismiss_anchor = '<form name="gs-notice-dismiss" action="" method = "post"><input type="hidden" name="cachedir_dismiss" value="yes"/></form>';
	    if(!file_exists(GS_API_CACHE_DIR)){
		if(!mkdir(GS_API_CACHE_DIR, 0766)){
		    gs_warn('Unable to create the cache directory at '.GS_API_CACHE_DIR." $cachedir_help_link");
		}
	    }
	    elseif(!is_writeable(GS_API_CACHE_DIR)){
		    gs_warn('The cache directory at '.GS_API_CACHE_DIR.' is not writeable.<br />Data for GoSquared widgets will not be cached. '.$cachedir_help_link.'<br />To fix this, change the permissions of this directory to 766 '.$permissions_help);
		}

	    ?>
	    <p>
		Go to you <a href='<?php echo site_url(); ?>/wp-admin/widgets.php'>Wordpress Widget settings</a> to enable GoSquared widgets.
	    </p>
	    <p>
		Ensure you <b>enter both your Site Token and API Key</b> in the fields below first.    
	    </p>
	    
	    <a href='<?php echo site_url(); ?>/wp-admin/widgets.php'><img src="<? echo WP_PLUGIN_URL; ?>/gosquared-livestats/wordpress_plugin_01_150x160.png" align="top" alt="GoSquared Widget 01" class="hero_preview"/></a>
	    <a href='<?php echo site_url(); ?>/wp-admin/widgets.php'><img src="<? echo WP_PLUGIN_URL; ?>/gosquared-livestats/wordpress_plugin_02_150x160.png" align="top" alt="GoSquared Widget 02" class="hero_preview"/></a>
	    <a href='<?php echo site_url(); ?>/wp-admin/widgets.php'><img src="<? echo WP_PLUGIN_URL; ?>/gosquared-livestats/wordpress_plugin_03_170x160.png" align="top" alt="GoSquared Widget 03" class="hero_preview"/></a>
	    
	</div>
	
	<form name="gs-options" action="" method = "post">

	    <h2>Site Token - Start tracking "<?php echo get_bloginfo('name'); ?>" with GoSquared.</h2>
	   
	   	<p>Your Site Token enables GoSquared to monitor your Wordpress site's traffic. <a href="https://www.gosquared.com/join/" title="Sign up to GoSquared for free to start monitoring your site in real-time" target="_blank">Sign up for free</a> to register your site with GoSquared.</p>


	    <div class="input-field">
		<span class="input-label">Your GoSquared Site Token </span>
		<input class="gs-text-input" type="text" name="gs_acct" value = "<?= $default_text ?>" 
		       onclick="if(this.value=='<?= $default_text ?>')this.value=''" 
		       onblur="if(this.value=='')this.value='<?= $default_text ?>'"/>&nbsp;
		<a href="http://www.gosquared.com/support/wiki/faqs#faq-site-token" target="_blank">What's this?</a>
	    </div>

		<h2>API Key - Share your stats via GoSquared Widgets.</h2>
		
		<p>Your API Key enables you to share your stats with your blog visitors via Widgets. Widgets will not work without an API Key.</p>  

		<div class="input-field">
		<span class="input-label">Your GoSquared API Key </span>
		<input class="gs-text-input" type="text" name="gs_apiKey" value = "<?= $default_apiKey ?>"
		       onclick="if(this.value=='<?= $default_apiKey ?>')this.value=''"
		       onblur="if(this.value=='')this.value='<?= $default_apiKey ?>'"/>&nbsp;
		<a href="http://www.gosquared.com/support/wiki/faqs#faq-API-key" target="_blank">What's this?</a>
		</div>

		<h2>Advanced Settings</h2>
		<table class="gs-settings">	    
		<tr>
		    <td class="label">Track admin pages </td>
		    <td><input type="radio" name="gs_trackAdmin" value="Yes" id="trackAdmin" <?php if ($trackAdmin == 'Yes')
		    echo 'checked="checked" '; ?>/> Yes</td>
		    <td><input type="radio" name="gs_trackAdmin" value="No" id="trackAdmin" <?php if ($trackAdmin == 'No')
		    echo 'checked="checked" '; ?>/> No</td>
		</tr>
		<tr>
		    <td class="label">Track post preview pages</td>
		    <td><input type="radio" name="gs_trackPreview" value="Yes" id="trackPreview" <?php if ($trackPreview == 'Yes')
		    echo 'checked="checked" '; ?>/> Yes</td>
		    <td><input type="radio" name="gs_trackPreview" value="No" id="trackPreview" <?php if ($trackPreview == 'No')
		    echo 'checked="checked" '; ?>/> No</td>
		</tr>
		<tr>
		    <td class="label">Tag individual users with </td>
		    <td><input type="radio" name="gs_trackUser" value="Off" id="trackUser" <?php if ($trackUser == 'Off')
				   echo 'checked="checked" '; ?>/> Off</td>
		    <td><input type="radio" name="gs_trackUser" value="UserID" id="trackUser" <?php if ($trackUser == 'UserID')
				   echo 'checked="checked" '; ?>/> User ID</td>
		    <td><input type="radio" name="gs_trackUser" value="Username" id="trackUser" <?php if ($trackUser == 'Username')
				   echo 'checked="checked" '; ?> /> Username</td>
		    <td class="wide"><input type="radio" name="gs_trackUser" value="DisplayName" id="trackUser" <?php if ($trackUser == 'DisplayName')
				   echo 'checked="checked" '; ?>/> Display Name</td>
		</tr>
		<tr>
		    <td class="label">Set widget cache timeout</td>
		    <td><input type="radio" name="gs_cacheTimeout" value="5" id="chacheTimeout" <?php if ($cacheTimeout == 5)
	    echo 'checked="checked"'; ?>/>5s</td>
		    <td><input type="radio" name="gs_cacheTimeout" value="15" id="chacheTimeout" <?php if ($cacheTimeout ==
		15)
	    echo 'checked="checked"'; ?>/>15s</td>
		    <td><input type="radio" name="gs_cacheTimeout" value="30" id="chacheTimeout" <?php if ($cacheTimeout ==
		30)
	    echo 'checked="checked"'; ?>/>30s</td>
		    <td><input type="radio" name="gs_cacheTimeout" value="60" id="chacheTimeout" <?php if ($cacheTimeout ==
		60)
	    echo 'checked="checked"'; ?>/>60s</td>
		</tr>
	    </table>
	    <input type="submit" value="Save Settings" class="button-primary" />
        </form>
    </div>

    <?php
}

function gs_print_gstc() {
    $acct = get_option('gstc_acct');
    $trackAdmin = get_option('gstc_trackAdmin');
    $trackPreview = get_option('gstc_trackPreview');
    $trackUser = get_option('gstc_trackUser');

    //Check if we are not tracking admin pages and if this is an admin page then return
    if ($trackAdmin == 'No' && is_admin())
	return;

    //Check if we are not tracking preview pages and if this is a preview page then return
    if (isset($_GET['preview']) && $_GET['preview'] == 'true' && $trackPreview == 'No')
	return;

    if ($acct) {
	$gstc_userDetail = '';

	//If tracking names, get the relevant information
	if ($trackUser != 'Off') {
	    //Get current user
	    require_once(ABSPATH . WPINC . "/pluggable.php");
	    $current_user = wp_get_current_user();
	    //Check if current user is not a guest
	    if (0 != $current_user->ID) {
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
	    }
	}
	$params = array();
	$params['acct'] = $acct;
	if ($gstc_userDetail) {
	    $params['VisitorName'] = $gstc_userDetail;
	}
	wp_enqueue_script('gstc', WP_PLUGIN_URL . '/gosquared-livestats/tracker.js', '', false, true);
	wp_localize_script('gstc', 'GoSquared', $params);
    }
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


// widgets go here
add_action('wp_enqueue_scripts', 'gs_enqueue_scripts');
add_action('widgets_init', 'load_widget');

function gs_enqueue_scripts() {
    wp_enqueue_script('jquery');
}

function load_widget() {
    register_widget('WP_Widget_GS_OnlineVisitors');
}

class WP_Widget_GS_OnlineVisitors extends WP_Widget {

    function WP_Widget_GS_OnlineVisitors() {
	// widget actual processes
	$widget_ops = array('classname' => 'widget_gs_onlinevisitors', 'description' => __('Display the number of visitors currently on your site'));
	$control_ops = array('height' => 320, 'width' => '600', 'id_base' => 'gs_onlinevisitors');
	parent::__construct('gs_onlinevisitors', __('GoSquared Online Visitors'), $widget_ops, $control_ops);
    }

    function form($instance) {
	// outputs the options form on admin
	// set default options
	$defaults = array('style' => 1);
	$instance = wp_parse_args((array) $instance, $defaults);
	?>
	<h2>GoSquared</h2>

	<h3>Choose the style for this widget</h3>
		   <?php $wstyle = $instance['style']; ?>

	<style type="text/css">
	    .gs-widget-label .gs-img-container{
		display: inline-block;
		width: 180px;
		height: 180px;
		position: relative;
		text-align: center;
	    }
	    .gs-widget-label{
		display: inline-block;
		text-align: center;
	    }
	    .gs-widget-label .gs-img-container{
		display: block;
	    }
	</style>
	<label for="gs-widget-option-1" class="gs-widget-label">
	    <div class="gs-img-container"><img src="<? echo WP_PLUGIN_URL; ?>/gosquared-livestats/wordpress_plugin_01_150x160.png" align="top" alt="GoSquared Widget 01"/></div>
	    <input type="radio" id="gs-widget-option-1" name="<?php echo $this->get_field_name('style'); ?>" 
	<?php if (1 == $wstyle)
	    echo 'checked'; ?> value="1" />
	</label>

	<label for="gs-widget-option-2" class="gs-widget-label">
	    <div class="gs-img-container"><img src="<? echo WP_PLUGIN_URL; ?>/gosquared-livestats/wordpress_plugin_02_150x160.png" align="top" alt="GoSquared Widget 02"/></div>
	    <input type="radio" id="gs-widget-option-2" name="<?php echo $this->get_field_name('style'); ?>" 
	<?php if (2 == $wstyle)
	    echo 'checked'; ?> value="2" />
	</label>

	<label for="gs-widget-option-3" class="gs-widget-label">
	    <div class="gs-img-container"><img src="<? echo WP_PLUGIN_URL; ?>/gosquared-livestats/wordpress_plugin_03_170x160.png" align="top" alt="GoSquared Widget 03"/></div>
	    <input type="radio" id="gs-widget-option-3" name="<?php echo $this->get_field_name('style'); ?>"
	<?php if (3 == $wstyle)
	    echo 'checked'; ?> value="3" />
	</label>

	<?php
    }

    function update($new_instance, $old_instance) {
	// processes widget options to be saved
	$instance = $old_instance;
	$instance['style'] = $new_instance['style'];
	return $instance;
    }

    function widget($args, $instance) {
	extract($args);
	$title = apply_filters('widget_title', $instance['title']);
	echo $before_widget;
	$cache_timeout = get_option('gstc_cacheTimeout');
	if(!$cache_timeout) $cache_timeout = DEFAULT_CACHE_TIMEOUT;
	if ($title)
	    echo $before_title . $title . $after_title;
	// set local script settings
	?> <script type="text/javascript">
		    var plugin_proxy_url = '<?php echo '?gs_api_proxy&widget=' ?>';
		    var cache_timeout = (+'<?php echo $cache_timeout ?>' || 30) * 1000; // seconds -> milliseconds 
	</script><?php
	$widget_url = WP_PLUGIN_DIR . "/gosquared-livestats/widgets/gs-onlinevisitors/" . $instance['style'] . "/gs-onlinevisitors.html";
	echo file_get_contents($widget_url);
	echo $after_widget;
    }

}

/**
 * Intercept response if gs_api_proxy param is set
 */

function api_proxy(){
    if(isset($_GET['gs_api_proxy'])){
	include 'apiproxy.php';
    }
}

api_proxy();
?>
