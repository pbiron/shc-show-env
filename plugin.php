<?php

/*
 * Plugin Name: Show Environment in Admin Bar
 * Description: Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Staging, QA, Dev, etc)
 * Plugin URI: https://github.com/pbiron/shc-show-env
 * Version: 1.1
 * Author: Paul V. Biron/Sparrow Hawk Computing
 * Author URI: http://sparrowhawkcomputing.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright 2017-2018 Paul V. Biron/Sparrow Hawk Computing
 * @package plugins
 * @subpackage shc-show-env
 */

defined( 'ABSPATH' ) || die();

/**
 * Main plugin class.
 *
 * @since 1.1
 */
class SHC_Show_Env
{
	/**
	 * Our plugin instance.
	 *
	 * @since 1.1
	 *
	 * @var SHC_Show_Env
	 */
	protected static $instance;

	/**
	 * Our version number.
	 *
	 * @since 1.1
	 *
	 * @var number
	 */
	const version = 1.1;

	/**
	 * Constrcutor.
	 *
	 * @since 1.1
	 *
	 * @return SHC_Show_Env
	 */
	function __construct() {
		if ( self::$instance ) {
			return self::$instance;
		}
		self::$instance = $this;

		add_action( 'after_setup_theme', array( $this, 'init' ) );
	}

	/**
	 * Decide whether we should be hidden, and if not add hooks.
	 *
	 * @since 1.1
	 *
	 * @return void
	 *
	 * @action after_theme_setup
	 */
	function init() {
		/**
		 * Filters whether our node should be hidden in the Admin Bar.
		 *
		 * If our node is hidden, then this plugin is basically a no-op.
		 *
		 * @since 1.0
		 *
		 * @param bool $hide Whether our node should be hidden in the Admin Bar.
		 */
		if ( apply_filters( 'shc_show_env_hide', ! is_admin_bar_showing() ) ) {
			return;
		}

		add_action( 'init', array( $this, 'register_styles' ), 9 );

		// priority=1 is to get it as far to the right as possible,
		// hopefully to the right of my-account
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_admin_bar_node' ), 1 );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );

		return;
	}

	/**
	 * Add a node to the admin bar that identifies the environment.
	 *
	 * @since 1.1
	 *
	 * @return void
	 *
	 * @action admin_bar_menu
	 */
	static function add_admin_bar_node() {
		/**
		 * @global WP_Admin_Bar $wp_admin_bar Global admin bar object.
		 */
		global $wp_admin_bar;

		list( $name, $class ) = self::id_env();

		// ensure $class is a single token
		$class = str_replace( ' ', '-', $class );
		$args = array(
			'id' => 'shc-show-env',
			'parent' => 'top-secondary',
			'title' => $name,
			'meta'   => array( 'class' => "shc-show-env $class" ),
		);
		$wp_admin_bar->add_node( $args );

		return;
	}

	/**
	 * Identify the environment we're running in.
	 *
	 * If the environment is represented by a custom class (via any of
	 * the `SHC_SHOW_ENV_CUSTOM` environment variable, the `SHC_SHOW_ENV_CUSTOM`
	 * PHP constant, or the `shc_show_env_id_env` filter), it is the responsibility
	 * of the entity that defined that custom class to ensure that CSS styles
	 * are enqueued that define how that custom class is to be rendered in the
	 * Admin Bar.
	 *
	 * @since 1.1
	 *
	 * @return array $env {
	 *     @type string $0 The environment name to show in the Admin Bar.
	 *     @type string $1 The class of the env.  One of 'prod', 'staging', 'qa', 'dev',
	 *     				   or a custom class.
	 * }
	 *
	 * @see shc_show_env_id_env
	 */
	static function id_env() {
 		// if one of our environment variables is defined (e.g., in the web server configuration),
 		// return that...without filtering it
		if ( getenv( 'SHC_SHOW_ENV_PROD' ) ) {
			return array( getenv( 'SHC_SHOW_ENV_PROD' ), 'prod' );
		}
		elseif ( getenv( 'SHC_SHOW_ENV_STAGING' ) ) {
			return array( getenv( 'SHC_SHOW_ENV_STAGING' ), 'staging' );
		}
		elseif ( getenv( 'SHC_SHOW_ENV_QA' ) ) {
			return array( getenv( 'SHC_SHOW_ENV_QA' ), 'qa' );
		}
		elseif ( getenv( 'SHC_SHOW_ENV_DEV' ) ) {
			return array( getenv( 'SHC_SHOW_ENV_DEV' ), 'dev');
		}
		elseif ( getenv( 'SHC_SHOW_ENV_CUSTOM' ) ) {
			return array( getenv( 'SHC_SHOW_ENV_CUSTOM' ), 'custom');
		}
		// if one of our constants is defined (e.g., in wp-config.php),
 		// return that...without filtering it
		elseif ( defined( 'SHC_SHOW_ENV_PROD' ) ) {
			return array( SHC_SHOW_ENV_PROD, 'prod' );
		}
		elseif ( defined( 'SHC_SHOW_ENV_STAGING' ) ) {
			return array( SHC_SHOW_ENV_STAGING, 'staging' );
		}
		elseif ( defined( 'SHC_SHOW_ENV_QA' ) ) {
			return array( SHC_SHOW_ENV_QA, 'qa' );
		}
		elseif ( defined( 'SHC_SHOW_ENV_DEV' ) ) {
			return array( SHC_SHOW_ENV_DEV, 'dev' );
		}
		elseif ( defined( 'SHC_SHOW_ENV_CUSTOM' ) ) {
			return array( SHC_SHOW_ENV_CUSTOM, 'custom' );
		}

		/* translators: abbreviation for 'Production' */
		$env = array( __( 'Prod', 'show-environment-in-admin-bar' ), 'prod' );

		if ( preg_match('/^(127|192\.168|169\.254)\./', $_SERVER['SERVER_ADDR'] ) ||
				'localhost' === $_SERVER['SERVER_NAME'] ) {
				/* translators: abbreviation for 'Localhost' */
			$env = array( __( 'Local', 'show-environment-in-admin-bar' ), 'dev' );
		}
		elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			/* translators: abbreviation for 'Development' */
			$env = array( __( 'Dev', 'show-environment-in-admin-bar' ), 'dev' );
		}

		/**
		 * Filters the identified environment.
		 *
		 * @since 1.0
		 *
		 * @param array $env {
		 *     @type string $0 The environment name to show in the Admin Bar.
		 *     @type string $1 The class of the env.  One of 'prod', 'staging', 'qa', 'dev',
		 *     				   or a custom class.
		 * }
		 */
		return apply_filters( 'shc_show_env_id_env', $env );
	}

	/**
	 * Register our styles.
	 *
	 * plugins/themes that define a custom environment class should use our
	 * styles handle as a dependency in their call to `wp_register_style()`
	 * or `wp_enqueue_style()` to guarantee that our styles are enqueued
	 * before theirs.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	function register_styles() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'shc-show-env', plugins_url( "assets/css/styles{$suffix}.css", __FILE__ ), array(), self::version ) ;

		return;
	}

	/**
	 * Enqueue our styles.
	 *
	 * @since 1.1
	 *
	 * @return void
	 *
	 * @action wp_enqueue_scripts
	 * @action admin_enqueue_scripts
	 */
	static function enqueue_styles() {
		wp_enqueue_style( 'shc-show-env' ) ;

		return;
	}
}

// instantiate our plugin class
new SHC_Show_Env();

