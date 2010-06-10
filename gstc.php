<?php
/*
Plugin Name: GoSquared
Plugin URI: http://www.gosquared.com/
Description: The official GoSquared Wordpress plugin to load the Tracking Code for GoSquared applications
Version: 0.2
Author: GoSquared
Author URI: http://www.gosquared.com/about/
*/

add_option('gstc_acct');
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
	$page = add_options_page('LiveStats', 'LiveStats', 'manage_options', 'gs-livestats', 'gs_options_page');
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
	
	if(isset($_POST['gs_acct'])){
		// Handle submission
		$acct = $_POST['gs_acct'];
		if(preg_match('/GSN-[0-9]{6,7}-[A-Z]{1}/', $acct)){
			update_option('gstc_acct', $acct);
			echo "<br />";
			gs_success('Account code updated successfully');
		}
		else{
			echo "<br />";
			gs_fail('Account code not of valid format. Must be like GSN-000000-X');
		}
	}
	
	$acct = get_option('gstc_acct');
	if(!$acct) $default_text = 'GSN-000000-X';
	else $default_text = $acct;
	?>
	<div class="wrap">
		<br />
		<img src="http://www.gosquared.com/images/G.png" alt="GoSquared"/>
		<h2>Tracking Code</h2>
		<form name="gs-options" action="" method = "post">
			Your site account code: <input type="text" name="gs_acct" value = "<?=$default_text?>" onclick="if(this.value=='<?=$default_text?>')this.value=''" onblur="if(this.value=='')this.value='<?=$default_text?>'"/>
			<a href="http://www.gosquared.com/support/wiki/wordpress_plugin" target="_blank">What's this?</a><br />
			<input type="submit" value="Save" />
		</form>
	</div>
	
	<?php
	

}

function gs_print_gstc(){
	$acct = get_option('gstc_acct');
	if($acct){
		$protocol = 'http://';
		if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) $protocol = 'https://';
		wp_enqueue_script('gstc', "{$protocol}www.gosquared.com/livestats/tracker?a=$acct", '', '', true);
	}
}

?>