<?php
ini_set("display_errors", "On");

define('API_KEY', get_option('gstc_apiKey'));
define('SITE_TOKEN', get_option('gstc_acct'));
$cache_timeout = get_option('gstc_cacheTimeout');
if(!$cache_timeout || $cache_timeout < 5) $cache_timeout = 5;
define('CACHE_TIMEOUT', $cache_timeout);


if (!defined('CACHE_TIMEOUT'))
    define('CACHE_TIMEOUT', 30);

// set acceptable values
$functions = array('aggregateStats', 'concurrents', 'geo', 'organics',
                   'pages', 'referrers', 'trends', 'visitors', 'list_sites');
$formats = array('json', 'xml');
$limits = array( 'aggregateStats' => 'aggreagateStatsLimit', 
                 'geo' => 'geoLimit',
                 'organics' => 'organicsLimit',
                 'pages' => 'pagesLimit',
                 'referrers' => 'referrersLimit',
                 'visitors' => 'visitorsLimit' );

// clean the name of the widget 
$widget = basename($_GET['widget']);

// load cached
$cachedDataJSON = loadCached($widget);
if ($cachedDataJSON == null) {
    $latest = update_cache($widget);
    echo $latest;
    exit();
} else {
    $cachedData = json_decode($cachedDataJSON, true);
    $timeSinceCache = time() - $cachedData["cache_time"];
    if ($timeSinceCache > CACHE_TIMEOUT) {
        $latest = update_cache($widget);
        echo $latest;
        exit();
    } else {
        echo $cachedDataJSON;
        exit();
    }
}

function loadCached($widget) {
    $path_to_cache = './apicache/'.$widget;
    if (file_exists($path_to_cache))
        return file_get_contents($path_to_cache);
    else return null;
}

function getData($widget) {
    // build request url
    $request = "http://api.gosquared.com/$func.$format?api_key=".API_KEY."&site_token=".SITE_TOKEN;
    // add limit if defined
    if ($limit) $request .= "&$param_limit=$limit";
    // if trends append trends params
    foreach ($trends_params as $key => $value)
    {
        $request .= '&'.$key.'='.$value;
    }
    // fetch api request
    return file_get_contents($request);
}

function update_cache($widget) {
    $path_to_config = './widgets/'.$widget.'/config.php';
    $request = buildRequestUrl($path_to_config);
    $latestJSON = file_get_contents($request);
    $latest = json_decode($latestJSON, true);
    $latest["cache_time"] = time();
    $latestJSON = json_encode($latest);
    file_put_contents("./apicache/".$widget, $latestJSON);
    return $latestJSON;
}

function buildRequestUrl($path_to_config) {
    global $functions, $formats, $limits, $widget;
    // include config file for widget
    include $path_to_config;

    $limit = null;
    $func = null;
    $param_limit = 'limit';
    $format = 'json'; // default data format
    $trends_params = array(); // only used for trends

    // check for function
    if (defined('FUNC') && FUNC != '' && in_array(FUNC, $functions)) 
    {
	$func = FUNC;   
    } 
    else 
    { // fail if function not supplied in config
	echo "Error: FUNC missing or incorrect in config [".$widget."]";
	exit(1); 
    }

    // if using trends
    if ($func == 'trends')
    {
	// get metric
	if (defined('METRIC') && METRIC != '')
	{
	    $trends_params['metric'] = METRIC;
	}
	else
	{
	    echo "Error: invalid or missing trends metric [".$widget."]";
	    exit(1);
	}

	// get optional args
	if (defined('TIMEZONE') && TIMEZONE != '')
	    $trends_params['timezone'] = TIMEZONE;
	if (defined('GROUPING') && GROUPING != '')
	    $trends_params['grouping'] = GROUPING;
	if (defined('TF_START') && TF_START != '')
	    $trends_params['start'] = TF_START;
	if (defined('TF_END') && TF_END != '')
	    $trends_params['end'] = TF_END;
	if (defined('TF_PERIOD') && TF_PERIOD != '')
	    $trends_params['period'] = TF_PERIOD;
    }

    // check for limit
    if (defined('LIMIT') && LIMIT != '') 
    { // if limit is defined keep it
	$limit = LIMIT;

	// fix for other types of api limits
	if (array_key_exists($func, $limits)) 
	{
	    $param_limit = $limits[$func];
	}
    } // if not, keep limit null

    // check for format specifier
    if (defined('FORMAT') && in_array(FORMAT, $formats)) {
	$format = FORMAT;
    }

    // build request url
    $request = "http://api.gosquared.com/$func.$format?api_key=".API_KEY."&site_token=".SITE_TOKEN;
    // add limit if defined
    if ($limit) $request .= "&$param_limit=$limit";
    // if trends append trends params
    foreach ($trends_params as $key => $value)
    {
	$request .= '&'.$key.'='.$value;
    }
    // fetch api request
    return $request;
}
