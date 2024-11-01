<?php
/**
 * Created by compemperor.
 * Date: 2016-07-24
 * Time: 22:49
 */

/*
Plugin Name: Social Shadow
Description: A simple plugin that pulls information from a general facebook feed and creates a post of every feed.
Version: 1.0
Author: compemperor
Text Domain: FG
*/

define('socshw_socshwdir',__FILE__);

//includes
include ('includes/facebook_data.php');
include ('includes/activate.php');
include ('includes/deactivate.php');
include ('includes/custom_event.php');
include ('includes/admin/init.php');
include ('includes/admin/menus.php');
include ('includes/plugin_globals.php');
include ('includes/front/load_front_styles_scripts.php');



//hooks
register_activation_hook(__FILE__,'socshw_start_cron_job');
add_action('admin_menu','socshw_admin_menus');
add_action('admin_init','socshw_admin_init');
add_action('socshw_userdef_facebook_pull_hook','socshw_get_facebook_posts');
add_action('switch_theme','socshw_clear_cron_jobs'); //kill the cron jobs when the theme is switched
add_action('wp_enqueue_scripts','socshw_load_front_data');
register_deactivation_hook(__FILE__,'socshw_clear_cron_jobs'); //kill the cron jobs when the plugin is deactivated.

//filters
add_filter('cron_schedules','socshw_addCronMinutes');


//Localize
add_action('plugins_loaded', 'socshw_load_textdomain');
function socshw_load_textdomain() {
    load_plugin_textdomain( 'FG', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}