<?

add_option('gstc_acct');
add_option('gstc_trackAdmin');
add_option('gstc_trackPreview');
add_option('gstc_trackUser');
add_option('gstc_apiKey');
add_action('admin_init', 'gs_init');
add_action('admin_menu', 'gs_options');

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

function gs_options_page() {
    global $style_file, $style_url;

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $fail_msg = "";
    $success_msg = "";
    $warn_msg = "";
    if (isset($_POST['gs_acct'])) {
        // Handle submission
        $acct = $_POST['gs_acct'];
        $apiKey = $_POST['gs_apiKey'];
        $trackAdmin = isset($_POST['gs_trackAdmin']) ? $_POST['gs_trackAdmin'] : 'Yes';
        $trackPreview = isset($_POST['gs_trackPreview']) ? $_POST['gs_trackPreview'] : "Yes";
        $trackUser = isset($_POST['gs_trackUser']) ? $_POST['gs_trackUser'] : 'Username';
        $cacheTimeout = isset($_POST['gs_cacheTimeout']) ? $_POST['gs_cacheTimeout'] : 'Yes';
        $valid_acct = preg_match('/^GSN-[0-9]{6,7}-[A-Z]{1}$/', $acct);
        $valid_apiKey = $apiKey == "" ? 1 : preg_match('/^[0-9A-Z]{16}$/', $apiKey);
        if ($valid_acct) {
            update_option('gstc_acct', $acct);
            update_option('gstc_trackAdmin', $trackAdmin);
            update_option('gstc_trackPreview', $trackPreview);
            update_option('gstc_trackUser', $trackUser);
            $success_msg = 'Settings updated successfully';
        } else {
            if (!$valid_acct) {
                $fail_msg .= '<p>Site token not of valid format. Must be like GSN-000000-X</p>';
            }
            if(!$fail_msg) $fail_msg = 'An error occurred';
        }
        if ($valid_apiKey) {
            update_option('gstc_apiKey', $apiKey);
        } else {
            $fail_msg .= '<p>API key not of valid format. Must be 16 characters long and only contain capital letters and numbers</p>';
            $success_msg = "";
            $warn_msg .= '<p>Some settings could not be saved.</p>';
        }
    }
    print_gs_options_page($fail_msg, $success_msg, $warn_msg);
}

function print_gs_options_page($fail_msg, $success_msg, $warn_msg) {
    $acct = get_option('gstc_acct');
    $apiKey = get_option('gstc_apiKey');
    $trackAdmin = get_option('gstc_trackAdmin');
    $trackPreview = get_option('gstc_trackPreview');
    $trackUser = get_option('gstc_trackUser');
    $cacheTimeout = get_option('gstc_cacheTimeout');
    include 'gs_options_page_template.php';
}
