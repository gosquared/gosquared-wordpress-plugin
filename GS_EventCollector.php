<?php
/*  Copyright 2012 GoSquared (email : support@gosquared.com)

    This file is part of GoSquared WooCommerce Plugin.

    GoSquared WooCommerce Plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GoSquared WooCommerce Plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GoSquared WooCommerce Plugin.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * A class to store visitor-triggered events that would otherwise
 * be lost across page changes.
 *
 * An example of this is adding an item to cart. The item is added
 * with a post to the current page, but the page is then redirected
 * to the cart. In this instance printing the javascript event trigger
 * before the page is redirected is useless as it won't get a chance
 * to be run, and the add-to-cart event in WooCommerce has been
 * triggered before the location change. The event can be stored here
 * and printed at the next full page load.
 *
 * @author Jack Kingston
 */
class GS_EventCollector
{
    static private $store_name = "gs_events";
    static public $instance;
    public $eventList;

    private function __construct() {
        if (!session_id()) session_start();
        $this->eventList = $this->pullEventsFromSession();
    }

    private function pullEventsFromSession() {
        $store_name = GS_EventCollector::$store_name;
        $events = array();
        if (isset($_SESSION[$store_name]) && is_array($_SESSION[$store_name]))
            $events = $_SESSION[$store_name];
        return $events;
    }

    public function __destruct() {
        $this->saveEventsToSession();
    }

    public function addEvent($name, $val) {
        $this->eventList[$name] = $val;
    }

    private function saveEventsToSession() {
        $store_name = GS_EventCollector::$store_name;
        $_SESSION[$store_name] = $this->eventList;
    }

    static public function getInstance() {
        if (self::$instance)
            return self::$instance;
        else return self::createInstance();
    }

    static private function createInstance() {
        self::$instance = new GS_EventCollector();
        return self::$instance;
    }

    public function getJS() {
        $js = $this->generateJS();
        $js .= $this->getFooterJS();
        $this->clearEvents();
        return $js;
    }

    public function generateJS() {
        $js = "<script>"
            . "window.gsevents = window.gsevents || [];";
        foreach ($this->eventList as $name => $val)
            $js .= "window.gsevents.push(function () {"
                 .    "GoSquared.DefaultTracker.TrackEvent('$name', '$val');"
                 . "});";
        $js .= "</script>";
        return $js;
    }

    public function clearEvents() {
        $this->eventList = array();
    }

    public function getFooterJS() {
        $js = "<script>"
            . "(function() {"
            .   "function waitForTracker(f) {"
            .       "if (!GoSquared.DefaultTracker) {"
            .           "setTimeout(function() {waitForTracker(f);},500);"
            .       "} else {"
            .           "f();"
            .       "}"
            .   "}"
            .   "waitForTracker(function() {"
            .       "var gsevents = window.gsevents || [];"
            .       "for (var ev in gsevents) {"
            .           "gsevents[ev]();"
            .       "}"
            .   "});"
            . "})();"
            . "</script>";
        return $js;
    }

}
