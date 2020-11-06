<?php
/*
Plugin Name: XV Podcasts
Plugin URI: https://xavi.ivars.me/en/code/wordpress-plugins/xv-podcasts
Description: A very simple podcast plugin
Version: 1.0
Author: Xavi Ivars
Author URI: https://xavi.ivars.me
License: A "Slug" license name e.g. GPL2
Text Domain: xv-podcasts
*/

define( 'XV_PODCASTS_PATH', plugin_dir_path( __FILE__ ) );

include( XV_PODCASTS_PATH . 'src/class.podcast-content-type.php' );
include( XV_PODCASTS_PATH . 'src/class.podcast-feed.php' );

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

new XVPodcastContentType();
new XVPodcastFeed();