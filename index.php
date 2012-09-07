<?php
/*
    Plugin Name:    GoSquared
    Plugin URI:     http://gosquared.com
    Description:    The official WordPress plugin for GoSquared.
    Version:        0.4.1
    License:        GPL3 http://www.gnu.org/licenses/gpl.html
    Author:         GoSquared
    Author URI:     http://www.gosquared.com/about/
*/

//  Set a base path
    define('DIR', dirname(__FILE__) . '/');

//  And our custom theming functions go here.
    include_once DIR . 'functions.php';
        
//  Include another file
    include_once DIR . 'hooks.php';
    
//  Ah, look how lovely and clear this all is.
