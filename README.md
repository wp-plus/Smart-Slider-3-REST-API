# Smart Slider 3 REST API (Work In Progress...)

Simple REST API endpoints for [Smart Slider 3 WordPress plugin](https://wordpress.org/plugins/smart-slider-3/).

Currently only there is 1 endpoint implemented:
`/wp-json/smartslider3/v1/sliders/<slider-id>`.

## How to install

 1. Download the code and put it in a directory (e.g. `Smart-Slider-3-REST-API`) under your WordPress' `wp-content/plugins` directory.
 2. Activate the plugin from your WordPress' administration interface.

## How to use
Visit `http(s)://your.wordpress.domain/wp-json/smartslider3/v1/` to see the available routes and also check out the schema at `http(s)://your.wordpress.domain/wp-json/smartslider3/v1/sliders/schema`.

## TODOs
 - Check for the availability of Smart Slider 3 (free or PRO);
 - Tests;
 - CodeSniffer;
 - Travis CI;
 - Add new endpoint for getting a collection of sliders;
 - (Eventually) endpoints for creating / updating sliders.
 - Slide-related endpoints.
 - Enable installation with Composer;
 - Documentation.
