<?php
/*
Plugin Name: GoSquared
Plugin URI: http://www.gosquared.com/
Description: The official GoSquared Wordpress plugin to load the Tracking Code for GoSquared applications
Version: 0.2.3
Author: GoSquared
Author URI: http://www.gosquared.com/about/
Contributions by: Aaron Parker
*/

add_option('gstc_acct');
add_option('gstc_trackAdmin');
add_option('gstc_trackPreview');
add_option('gstc_trackUser');
add_action('admin_init', 'gs_init');
add_action('admin_menu', 'gs_options');
gs_print_gstc();

function gs_init()
{
	$style_url = WP_PLUGIN_URL .'/gosquared-livestats/gs.css';
	$style_file = WP_PLUGIN_DIR .'/gosquared-livestats/gs.css';
	/* Register our stylesheet. */
	wp_register_style('gs_style', $style_url);
}


function gs_options() {
	$page = add_options_page('GoSquared', 'GoSquared', 'manage_options', 'gs-livestats', 'gs_options_page');
	/* Using registered $page handle to hook stylesheet loading */
	add_action('admin_print_styles-' . $page, 'gs_admin_style');
}

function gs_admin_style(){
	wp_enqueue_style('gs_style');
}

function gs_success($message){
	echo '<div class="center"><div class="gs_success">'.$message.'</div></div>';
}

function gs_fail($message){
	echo '<div class="center"><div class="gs_fail">'.$message.'</div></div>';
}

function gs_options_page() {
	global $style_file, $style_url;
	
	if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
  <div class="wrap">
  <br />
  <div style="width: 100%; height: 50px; background: #1E1E1E url('<?php echo WP_PLUGIN_URL.'/gosquared-livestats/head_01_300x50.png';?>') no-repeat; background-position: left top; border: 1px solid #000000; -webkit-border-radius: 5px; -moz-border-radius: 5px; -o-border-radius: 5px; border-radius: 5px; -webkit-box-shadow: 0px 1px 0px #ffffff; -moz-box-shadow: 0px 1px 0px #ffffff; -o-box-shadow: 0px 1px 0px #ffffff; box-shadow: 0px 1px 0px #ffffff;" id="gosquaredlogo">
  </div>
    <?php
	
	if(isset($_POST['gs_acct'])){
		// Handle submission
		$acct = $_POST['gs_acct'];
		$trackAdmin = isset($_POST['gs_trackAdmin']) ? $_POST['gs_trackAdmin'] : 'Yes';
		$trackPreview = isset($_POST['gs_trackPreview']) ? $_POST['gs_trackPreview'] : "Yes";
		$trackUser = isset($_POST['gs_trackUser']) ? $_POST['gs_trackUser'] : 'Username';
		if(preg_match('/GSN-[0-9]{6,7}-[A-Z]{1}/', $acct)){
			update_option('gstc_acct', $acct);
			update_option('gstc_trackAdmin', $trackAdmin);
			update_option('gstc_trackPreview', $trackPreview);
			update_option('gstc_trackUser', $trackUser);
			echo "<br />";
			gs_success('Settings updated successfully');
		}
		else{
			echo "<br />";
			gs_fail('Account code not of valid format. Must be like GSN-000000-X');
		}
	}
	
	$acct = get_option('gstc_acct');
	$trackAdmin = get_option('gstc_trackAdmin');
	$trackPreview = get_option('gstc_trackPreview');
	$trackUser = get_option('gstc_trackUser');
	
	if(!$acct) $default_text = 'GSN-000000-X';
	else $default_text = $acct;
	
	if(!$trackAdmin) $trackAdmin = 'Yes';
	
	if(!$trackPreview) $trackPreview = 'Yes';
	
	if(!$trackUser) $trackUser = 'Username';
	?>
		<h2>Tracking Code</h2>
		<form name="gs-options" action="" method = "post">
			<table border="0" cellspacing="10" cellpadding="5">
				<tr><td>Your site account code: </td><td colspan=3><input type="text" name="gs_acct" value = "<?=$default_text?>" onclick="if(this.value=='<?=$default_text?>')this.value=''" onblur="if(this.value=='')this.value='<?=$default_text?>'"/>&nbsp;<a href="http://www.gosquared.com/support/wiki/wordpress_plugin" target="_blank">What's this?</a></td></tr>
				<tr><td>Track admin pages: </td><td><input type="radio" name="gs_trackAdmin" value="Yes" id="trackAdmin" <?php if($trackAdmin == 'Yes') echo 'checked="checked" '; ?>/> Yes</td><td><input type="radio" name="gs_trackAdmin" value="No" id="trackAdmin" <?php if($trackAdmin == 'No') echo 'checked="checked" '; ?>/> No</td></tr>
				<tr><td>Track post preview pages: </td><td><input type="radio" name="gs_trackPreview" value="Yes" id="trackPreview" <?php if($trackPreview == 'Yes') echo 'checked="checked" '; ?>/> Yes</td><td><input type="radio" name="gs_trackPreview" value="No" id="trackPreview" <?php if($trackPreview == 'No') echo 'checked="checked" '; ?>/> No</td></tr>
				<tr><td>Display users by: </td><td><input type="radio" name="gs_trackUser" value="Off" id="trackUser" <?php if($trackUser == 'Off') echo 'checked="checked" '; ?>/> Off</td><td><input type="radio" name="gs_trackUser" value="UserID" id="trackUser" <?php if($trackUser == 'UserID') echo 'checked="checked" '; ?>/> User ID</td><td><input type="radio" name="gs_trackUser" value="Username" id="trackUser" <?php if($trackUser == 'Username') echo 'checked="checked" '; ?> /> Username</td><td><input type="radio" name="gs_trackUser" value="DisplayName" id="trackUser" <?php if($trackUser == 'DisplayName') echo 'checked="checked" '; ?>/> Display Name</td></tr>
			</table>
			<input type="submit" value="Save" />
		</form>
	</div>
	
	<?php
	

}

function gs_print_gstc(){
	$acct = get_option('gstc_acct');
	$trackAdmin = get_option('gstc_trackAdmin');
	$trackPreview = get_option('gstc_trackPreview');
	$trackUser = get_option('gstc_trackUser');
	
	//Check if we are not tracking admin pages and if this is an admin page then return
	if ($trackAdmin == 'No' && is_admin()) return;
	
	//Check if we are not tracking preview pages and if this is a preview page then return
	if(isset($_GET['preview']) && $_GET['preview'] == 'true' && $trackPreview == 'No') return;
	
	if($acct){
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
		if($gstc_userDetail){
			$params['VisitorName'] = $gstc_userDetail;
		}
		wp_enqueue_script('gstc', WP_PLUGIN_URL .'/gosquared-livestats/tracker.js', '', false, true);
		wp_localize_script('gstc', 'GoSquared', $params);
	}
}

?>