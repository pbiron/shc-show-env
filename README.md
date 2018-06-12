# Show Environment in Admin Bar
Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Staging, QA, Dev, etc).

## Description

If you're like me, you often have multiple versions of the same WordPress site open in different browser windows, e.g.,  production in one window and development in another window.

And if you're like me, you have also unwittingly edited content in the production environment thinking you were doing so in the development environment or vice versa.

If so, then this plugin is for you!

It adds an indication of the current environment to the Admin Bar that is easier to see than examining the URL in your browser's address bar.

![Production](assets/images/screenshot-1.png?raw=true "Production")
![Staging](assets/images/screenshot-2.png?raw=true "Staging")
![QA](assets/images/screenshot-3.png?raw=true "QA")
![Development](assets/images/screenshot-4.png?raw=true "Development")
![Custom &mdash; QA](assets/images/screenshot-5.png?raw=true "Custom &mdash; Preview")

## Installation

Installation of this plugin works like any other plugin out there:

1. Upload the contents of the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

### Minimum Requirements

* WordPress 4.6 or greater (3.1 if you don't need a localized version)

## Out-of-the-box behavior

Out-of-the-box, 2 classes of environment are recongized:

1. Production

    * The node added to the Admin Bar has a red background (i.e., Stop/be carefull with any changes you make).
    * If neither a staging, QA, nor development environment is detected, then 'Prod' is displayed in the Admin Bar.

2. Development

    * The node added to the Admin Bar has a green background (i.e., Go ahead, it is safe to make changes).
    * If WP is running on localhost (either a loopback IP address or LAN IP address), then 'Local' displays in the Admin Bar.
    * If WP_DEBUG is defined & true, then 'Dev' displays in the Admin Bar.

## Customizing the out-of-the-box behavior

Two additional enviroments are supported, but cannot be automatically detected:

3. Staging

    * The node added to the Admin Bar has a yellow-ish background (i.e., Slow down, changes _might_ make it into the production site).
    * It is not possible to automatically detect a staging environment, so you will need to use the `SHC_SHOW_ENV_STAGING` environment variable, `SHC_SHOW_ENV_STAGING` PHP constant, or `shc_show_env_id_env` filter, see below.

4. QA

    * The node added to the Admin Bar has a blue background (i.e., "It's cool", changes you make won't affect the production site).
    * It is not possible to automatically detect a QA environment, so you will need to use the `SHC_SHOW_ENV_QA` environment variable, `SHC_SHOW_ENV_QA` PHP constant, or `shc_show_env_id_env` filter, see below.

### Setting web server environment variables

You can change the out-of-the-box behavior by defining 1 of 5 different environment variables (e.g., in the web server configuration) as follows:

1. `SHC_SHOW_ENV_PROD`
1. `SHC_SHOW_ENV_STAGING`
1. `SHC_SHOW_ENV_QA`
1. `SHC_SHOW_ENV_DEV`
1. `SHC_SHOW_ENV_CUSTOM`

If one of these environment variables is set, then it's value is used as the "name" of the environment in the Admin Bar.  If more than one of these web server environment variables is set (BTW, _you shouldn't do that!_), `SHC_SHOW_ENV_PROD` takes precedence, followed by `SHC_SHOW_ENV_STAGING`, followed by `SHC_SHOW_ENV_QA`, followed by `SHC_SHOW_ENV_DEV`, and finally `SHC_SHOW_ENV_CUSTOM`.

If one of these environment variables is set, then the PHP constants discussed below are not examined nor is the `shc_show_env_id_env` filter applied!

If `SHC_SHOW_ENV_CUSTOM` is set, then it is your responsibility to enqueue CSS rules for how that custom class should be formatted.  See below.

### Defining constants in wp-config.php

You can also change the out-of-the-box behavior by defining 1 of 5 different PHP constants (e.g., in wp-config.php) as follows:

```PHP
// you can define one of these 5 PHP constants
define( 'SHC_SHOW_ENV_PROD', 'Production' );
define( 'SHC_SHOW_ENV_STAGING', 'Staging' );
define( 'SHC_SHOW_ENV_QA', 'QA' );
define( 'SHC_SHOW_ENV_DEV', 'Development' );
define( 'SHC_SHOW_ENV_CUSTOM', 'Preview' );

```

If one of these PHP constants is defined, then it's value is used as the "name" of the environment in the Admin Bar.  If more than one of these constants is defined (BTW, _you shouldn't do that!_), `SHC_SHOW_ENV_PROD` takes precedence, followed by `SHC_SHOW_ENV_STAGING`, followed by `SHC_SHOW_ENV_QA`, followed by `SHC_SHOW_ENV_DEV`, and finally `SHC_SHOW_ENV_CUSTOM`.

If one of these PHP constants is defined, then the `shc_show_env_id_env` filter is **not** applied!

### Hooking into the shc_show_env_id_env filter

You can also change the out-of-the-box behavior by hooking into the `shc_show_env_id_env` filter.

The function you hook into this filter should return an (indexed) array of strings.  The value in index 0 is the "name" of the environment to display in the Admin Bar.  The value in index 1 is the CSS class of the environment (e.g., 'prod', 'staging', 'qa', 'dev', or a custom class).

If you return a custom CSS class, then it is your responsibility to enqueue CSS rules for how that custom class should be formatted.  See below.

For example,

```PHP
add_filter( 'shc_show_env_id_env', 'my_show_env_id_env' );
function my_show_env_id_env( $env ) {
	// match staging.example.com
	if ( preg_match( '/\bstaging\b/i', $_SERVER['HTTP_HOST'] ) ) {
		return array( 'Staging', 'staging' );
	}

	// match qa.example.com
	if ( preg_match( '/\bqa\b/i', $_SERVER['HTTP_HOST'] ) ) {
		return array( 'QA', 'qa' );
	}

	return $env;
}
```

### Enqueueing CSS rules for a custom class

If you return a custom CSS class from the function you hook to `shc_show_env_id_env`, then it is your responsibility to enqueue CSS to style that custom class.  If you use either the `SHC_SHOW_ENV_CUSTOM` environment variable or the `SHC_SHOW_ENV_CUSTOM` PHP constant, the CSS you enqueue should use the `custom` class.

For example, to style the 'preview' custom class in the example above, you could do:

```PHP
add_action( 'init', 'my_plugin_init' );
function my_plugin_init() {
	if ( is_admin_bar_showing() ) {
		add_action( 'wp_enqueue_scripts', 'my_show_env_enqueue' );
		add_action( 'admin_enqueue_scripts', 'my_show_env_enqueue' );
	}
}

function my_show_env_enqueue() {
	wp_enqueue_style( 'my_show_env', plugins_url( 'css/my_show_env_styles.css', __FILE__ ), array( 'shc-show-env' ) );
}

```

Notice that the call to `wp_enqueue_style()` above includes `array( 'shc-show-env' )` as the 3rd parameter.  This ensures that your custom CSS rules are enqueued **after** the styles from this plugin.  For more info, see [wp_enqueue_style](https://developer.wordpress.org/reference/functions/wp_enqueue_style/). 

`css/my_show_env_styles.css` should contain something like:

```CSS
#wpadminbar .ab-top-menu .shc-show-env.preview .ab-item,
#wpadminbar .ab-top-menu .shc-show-env.preview:hover .ab-item {
	background-color: #dd823b ;
}
```

The background-color you choose for your custom class should be relatively distinct from the background-colors used for all of the admin color schemes shipped with WP (after all, the whole point of this plugin is to make the indicator added to the Admin Bar stand out so you can easily see it and know which environment you are logged into).  If the background-color you choose is not sufficiently distinct, you should add additional styling to help it stand out.  For example, the stylesheet included with this plugin contains the following rule:

```CSS
/*
 * background-color for 'prod' is REAL close the admin bar background-color in the sunrise
 * Admin Color Scheme, so put a black border around it to help it stand out
 */
.admin-color-sunrise #wpadminbar .ab-top-menu .shc-show-env.prod .ab-item {
	border: 2px solid black ;
	box-sizing: border-box ;
	/* compensate for the border, so the text stays vertically centered */
	line-height: 28px;
}

```

### Conditionally hiding the environment in the Admin Bar

You can also conditionally hide the indication of the environment in the Admin Bar by hooking into the `shc_show_env_hide` filter.  This filter should return a boolean, with `true` meaning "hide the environment in the admin bar".  For example,

```PHP
add_filter( 'shc_show_env_hide', 'my_env_conditionally_hide' );
function my_env_conditionally_hide( $hide ) {
	return ! current_user_can( 'manage_options' );
}

```

## Changelog

### 1.1

* General code reorg
* Added support for the QA environment
* minor CSS fixes
* changed the Text Domain (for localization) to 'show-environment-in-admin-bar', so that the [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/show-environment-in-admin-bar) service can be used.

### 1.0.1

* Correct formatting in readme.txt
* Removed GitHub Plugin URL reader

### 1.0

* Initial release on .org

### 0.9

* PHP Coding Standards compliance

### 0.5.2

* Added ! defined( 'ABSPATH' ) security check

### 0.5.1

* Enqueue minified styles if `SCRIPT_DEBUG` is not defined

### 0.5

* Added `SHC_SHOW_ENV_(PROD|STAGING|DEV)` environment variables to env ID

### 0.1.2

* Added `shc_show_env_hide` filter, to allow conditional hiding of the admin-bar node

### 0.1.1

* Fixed typo in wp-config.php constants
* Made CSS for .admin-color-sunrise .shc-show-env.staging more distinct
* Added detailed Developer Notes

### 0.1

* Initial commit

## Ideas?
Please let me know by creating a new [issue](https://github.com/pbiron/shc-show-env/issues/new) and describe your idea.  
Pull Requests are welcome!

## Other Notes

I was inspired to write this plugin when I saw the [Blue Admin Bar](https://wordpress.org/plugins/blue-admin-bar/) plugin.  I thought that was a great idea, but having the background-color of the entire Admin Bar be different was a bit jarring.

## Buy me a beer

If you like this plugin, please support it's continued development by [buying me a beer](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Z6D97FA595WSU).
