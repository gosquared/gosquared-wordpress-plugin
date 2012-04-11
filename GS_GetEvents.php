<?php

include_once('GS_EventCollector.php');

$action_methods = array();

function add_gs_action_method($method_name) {
    global $action_methods;
    array_push($action_methods, $method_name);
}

function gs_add_event_to_session($name, $val) {
    GS_EventCollector::getInstance()->addEvent($name, $val);
}

add_action('admin_footer', 'gs_append_event_collector');
add_action('wp_footer', 'gs_append_event_collector');
add_action('login_footer', 'gs_append_event_collector');
function gs_append_event_collector() {
    $acct = get_option('gstc_acct');
    if ($acct) {
        $eventCollector = GS_EventCollector::getInstance();
        echo $eventCollector->getJS();
    }
}

add_action('init', 'get_events');
function get_events() {

    global $action_methods;
    foreach ($action_methods as $f) {
        call_user_func($f);
    }

    // for testing:
    $event_collector = GS_EventCollector::getInstance();

}

add_action('wp_login', 'gs_login_action');
function gs_login_action() {
    gs_add_event_to_session("Logged in", "");
}

add_action('wp_logout', 'gs_logout_action');
function gs_logout_action() {
    gs_add_event_to_session("Logged out", "");
}

add_action('comment_post', 'gs_comment_post_action');
function gs_comment_post_action() {
    gs_add_event_to_session("Comment posted", "");
}

