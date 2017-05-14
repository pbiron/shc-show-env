# Show Environment in Admin Bar
Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Staging, Dev, etc)

## Description
**Note**: this plugin is current in a "pre-alpha-release" state, and so it's behavior is constantly changing.

If you're like me, you often have multiple versions of the same WordPress site open in different browser windows, e.g.,  production in one window and development in another window.  And if you're like me, you have unwittingly edited content on the development environment thinking you were doing so in the production environment.  If so, then this plugin is for you!

This plugin adds an indication of the current environment to the Admin Bar that is easier to see than your browser's address bar.

![Production](assets/screenshot-1.png?raw=true "Production")
![Staging](assets/screenshot-2.png?raw=true "Staging")
![Development](assets/screenshot-3.png?raw=true "Development")
![Custom &mdash; QA](assets/screenshot-4.png?raw=true "Custom &mdash; QA")

## Installation
Installation of this plugin works like any other plugin out there:

1. Upload the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

### Minimum Requirements
* WordPress 3.1 or greater

## Changelog

### 0.1

* Initial commit

## Developer Notes

Out-of-the-box, 3 classes of environment are recongized:

1. Development
    * The node added to the Admin Bar has a green background (i.e., Go ahead, it is safe to make changes).
    * If WP is running on localhost (either a loopback IP address or LAN IP address), then 'Local' displays in the Admin Bar.
    * If WP_DEBUG is defined & true, then 'Dev' displays in the Admin Bar.
2. Staging
    * The node added to the Admin Bar has a yellow-ish background (i.e., Slow down, changes _might_ make it into the production site).
    * It is not possible to automatically detect a staging environment, so you will need to use the `SHC_SHOW_ENV_STAGING` constant or `shc_show_env_id_env` filter, see below.
3. Production
    * The node added to the Admin Bar has a red background (i.e., Stop/be carefull with any changes you make).
    * If neither a development nor staging environment is detected, then 'Prod' is displayed in the Admin Bar.

You can change that out-of-the-box behavior in 2 different ways:

=== Hooking into the `shc_show_env_id_env` filter

This filter should return an (indexed) array of strings.  The value in index 0 is the "name" of the environment to display in the Admin Bar.  The value in index 1 is the "class" of the environment (e.g., 'prod', 'staging', 'dev', or a custom class).  If you return a custom class, then you must also define CSS rules for how that custom class should be formatted.  See below.

For example,

```PHP
add_filter ('shc_show_env_id_env', 'my_show_env_id_env') ;

function
my_show_env_id_env ($env)
{
	// match staging.example.com
	if (preg_match ('/\bstaging\b/i', $_SERVER['HTTP_HOST']) {
		return (array ('Staging', 'staging')) ;
		}
	// match qa.example.com
	if (preg_match ('/\bqa\b/i', $_SERVER['HTTP_HOST']) {
		return (array ('QA', 'qa')) ;
		}
}
```

If you return a custom class in the function you hook to `shc_show_env_id_env`, then you also need to enqueue CSS to style that custom class.  For example, to style the 'qa' custom class in the example above, you could do:

```PHP
add_action ('init', 'my_show_env_init') ;

function
my_show_env_init ()
{
	if (is_admin ()) {
		add_action ('admin_enqueue_scripts', 'my_show_env_enqueue') ;
		}
	else if (is_admin_bar_showing ()) {
		add_action ('wp_enqueue_scripts', 'my_show_env_enqueue') ;
		}

	return ;
}

function
my_show_env_enqueue ()
{
	wp_enqueue_style ('my_show_env', plugins_url ('css/my_show_env_styles.css', __FILE__)) ;

	return ;
}
```

where `css/my_show_env_styles.css' contained:

```CSS
#wpadminbar .ab-top-menu .shc-show-env.qa .ab-item,
#wpadminbar .ab-top-menu .shc-show-env.qa:hover .ab-item
{
	background-color: #dd823b ;
}
```

The background-color you choose for your custom class(es) should be relatively distinct from the background-colors used for all of the admin color schemes shipped with WP (after all, the whole point of this plugin is to make the indicator added to the Admin Bar stand out so you can easily see it and know which environment you are logged into).  If the background-color you choose is not sufficiently distinct, you could add additional styling to help it stand out.  For example, the stylesheet included with this plugin contains the following rule:

```CSS
/*
 * our prod background-color is REAL close the admin bar background-color in the sunrise
 * Admin Color Scheme, so put a black border around it to help it stand out
 */
.admin-color-sunrise #wpadminbar .ab-top-menu .shc-show-env.prod .ab-item
{
	border: 2px solid black ;
	box-sizing: border-box ;
}
```

=== Defining constants in wp-config.php ===

You can also change the out-of-the-box behavior by defining 1 of 3 different constants (e.g., in wp-config.php) as follows:

```PHP
// you can define one of these 3 constants
define ('SHC_SHOW_ENV_PROD', 'Production') ;
define ('SHC_SHOW_ENV_STAGING', 'Staging') ;
define ('SHC_SHOW_ENV_DEV', 'Development') ;
```

If one of these constants is defined, then it's value is used as the "name" of the environment in the Admin Bar.  If more than one of these constants is defined (you shouldn't do that), `SHC_SHOW_ENV_PROD` takes precedence, followed by `SHC_SHOW_ENV_STAGING`, followed by `SHC_SHOW_ENV_DEV`.

If one of these 3 constants is defined, then the `shc_show_env_id_env` filter is **not** applied!

## Ideas?
Please let me know by creating a new [issue](https://github.com/pbiron/shc-add-env/issues/new) and describe your idea.  
Pull Requests are welcome!

## Other Notes

I was inspired to write this plugin when I saw the [Blue Admin Bar](https://wordpress.org/plugins/blue-admin-bar/) plugin.  I thought that was a great idea, but having the background-color of the entire Admin Bar be different was a bit jarring.

## Buy me a beer

If you like this plugin, please support it's continued development by [buying me a beer](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Z6D97FA595WSU).
