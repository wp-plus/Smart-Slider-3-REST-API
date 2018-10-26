<?php

/**
 * Plugin Name: Smart Slider 3 REST API
 * Plugin URI: https://github.com/WP-Plus/Smart-Slider-3-REST-API
 * Description: Simple REST API endpoints for Smart Slider 3.
 * Version: 0.1.0
 * Author: WP-Plus
 * Author URI: https://github.com/WP-Plus
 * License: MIT
 * License URI: https://github.com/WP-Plus/Smart-Slider-3-REST-API/blob/master/LICENSE
 */

namespace Wpp\SmartSlider3RestApi;

use N2Loader;

define('PLUGIN_BASE_PATH', dirname(__FILE__));

// Wait until plugins are loaded, so we can use some classes from SmartSlider3.
add_action(
    'plugins_loaded',
    function() {
        // Use Smart Slider 3's loader
        N2Loader::addPath('restapi', PLUGIN_BASE_PATH . '/includes');

        // Enable our custom REST API endpoints
        N2Loader::import('endpoints.wp-rest-smart-slider-3-controller', 'restapi');
        add_action('rest_api_init', function() {
            (new Endpoints\WP_REST_Smart_Slider_3_Controller())->register_routes();
        });
    },
    100 // Make sure it is called after SmartSlider3's smart_slider_3_plugins_loaded(), which has priority 30.
);

