=== Show Environment in Admin Bar ===
Contributors: pbiron
Tags: admin, admin-bar
Requires at least: 3.1
Tested up to: 4.8
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Z6D97FA595WSU

Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Staging, Dev, etc)

== Description ==

**Note**: this plugin is current in a "pre-alpha-release" state, and so it's behavior is constantly changing.

If you're like me, you often have multiple versions of the same WordPress site open in different browser windows, e.g.,  production in one window and development in another window.  And if you're like me, you have unwittingly edited content on the development environment thinking you were doing so in the production environment.  If so, then this plugin is for you!

This plugin adds an indication of the current environment to the Admin Bar that is easier to see than your browser's address bar.

== Installation ==

Installation of this plugin works like any other plugin out there:

1. Upload the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Production
2. Staging
3. Development
4. Custom &mdash; QA

== Changelog ==

= 0.1 =

* Initial commit

== Developer Notes ==

@todo document the `shc_show_env_id_env` filter and how to add CSS for custom environment classes

== Other Notes ==

I was inspired to write this plugin when I saw the [Blue Admin Bar](https://wordpress.org/plugins/blue-admin-bar/) plugin.  I thought that was a great idea, but having the background-color of the entire Admin Bar be different was a bit jarring.
