<?php
/*
 * Plugin Name: Show Environment in Admin Bar
 * Description: Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Dev, Local, etc)
 * Plugin URI: https://github.com/pbiron/shc-show-env
 * Version: 0.1
 * Author: Paul V. Biron/Sparrow Hawk Computing
 * Author URI: http://sparrowhawkcomputing.com
 * Text Domain: shc-show-env
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/pbiron/shc-show-env
 *
 * @copyright 2017 Paul V. Biron/Sparrow Hawk Computing
 */

add_action ('init', 'shc_show_env_init') ;

/**
 * Add appropriate actions
 *
 * @return void
 *
 * @action init
 */
function
shc_show_env_init ()
{
	// load our textdomain
	load_plugin_textdomain ('shc-show-env', false, dirname (plugin_basename (__FILE__)) . '/languages') ;

	// add our node to the admin bar
	// priority=1 is to get it as far to the right as possible,
	// hopefully to the right of my-account
	add_action ('admin_bar_menu', 'shc_show_env_add_node', 1) ;

	if (is_admin ()) {
		add_action ('admin_enqueue_scripts', 'shc_show_env_enqueue') ;
		}
	else if (is_admin_bar_showing ()) {
		add_action ('wp_enqueue_scripts', 'shc_show_env_enqueue') ;
		}

	return ;
}

/**
 * Add a node to the admin bar that identifies the environment
 *
 * @return void
 *
 * @action admin_bar_menu
 */
function
shc_show_env_add_node ()
{
	global $wp_admin_bar ;

	list ($name, $class) = shc_show_env_id_env () ;
	$class = str_replace (' ', '-', $class) ;

	$args = array (
		'id' => "shc-show-env",
		'parent' => 'top-secondary',
		'meta'   => array ('class' => "shc-show-env $class"),
		'title' => $name,
		) ;
	$wp_admin_bar->add_node ($args) ;

	return ;
}

/**
 * Identify the environment
 *
 * @return array Environment name in index 0 and environment class in index 1
 *
 * @todo write a better description
 */
function
shc_show_env_id_env ()
{
	// if one of our constants is defined (e.g., in wp-config.php), return that...
	// without filtering it
	if (defined ('SHC_SHOW_ENV_PROD')) {
		return (array (SHC_show_env_PROD, 'prod')) ;
		}
	else if (defined ('SHC_SHOW_ENV_STAGING')) {
		return (array (SHC_show_env_STAGING, 'staging')) ;
		}
	else if (defined ('SHC_SHOW_ENV_DEV')) {
		return (array (SHC_show_env_DEV, 'dev')) ;
		}

	/* translators: abbreviation for 'Production' */
	$env = array (__('Prod', 'shc-show-env'), 'prod') ;

	if (preg_match ('/^(127|192\.168|169\.254)\./', $_SERVER['SERVER_ADDR']) || 'localhost' === $_SERVER['SERVER_NAME']) {
		/* translators: abbreviation for 'Localhost' */
		$env = array (__('Local', 'shc-show-env'), 'dev') ;
		}
	else if (defined ('WP_DEBUG') && WP_DEBUG) {
		/* translators: abbreviation for 'Development' */
		$env = array (__('Dev', 'shc-show-env'), 'dev') ;
		}
	// @todo figured out a way to detect a staging env

	/**
	 * Filter the value of the environment
	 *
	 * @param array $env 
	 *     $0 The environment name to show in the Admin Bar
	 *     $1 The class of the env.  One of 'prod', 'staging', 'dev', or a custom class
	 * }
	 *
	 * @return array Environment name in index 0 and environment class in index 1
	 *
	 * @todo add better descriptions for @param, including how to define new classes
	 */
	return (apply_filters ('shc_show_env_id_env', $env)) ;
}

/**
 * Enqueue our stylesheet
 *
 * @return void
 *
 * @action admin_enqueue_scripts, wp_enqueue_scripts
 */
function
shc_show_env_enqueue ()
{
	wp_enqueue_style ('shc_show_env', plugins_url ('css/styles.css', __FILE__)) ;
	
	return ;
}
