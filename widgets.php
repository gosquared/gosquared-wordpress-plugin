<?
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

/**
 * Intercept response if gs_api_proxy param is set
 */

function api_proxy()
{
    if (isset($_GET['gs_api_proxy'])) {
        include 'apiproxy.php';
    }
}

api_proxy();
