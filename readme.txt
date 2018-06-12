=== Show Environment in Admin Bar ===

Contributors: pbiron
Tags: admin, admin-bar
Requires at least: 4.6
Tested up to: 4.9.6
Stable tag: 1.1
License: GPLv2 or later
License URL: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Z6D97FA595WSU

Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Staging, QA, Dev, etc).

== Description ==

If you're like me, you often have multiple versions of the same WordPress site open in different browser windows, e.g.,  production in one window and development in another window.

And if you're like me, you have also unwittingly edited content in the production environment thinking you were doing so in the development environment or vice versa.

If so, then this plugin is for you!

It adds an indication of the current environment to the Admin Bar that is easier to see than examining the URL in your browser's address bar.

== Out-of-the-box behavior ==

Out-of-the-box, 2 different environments are recognized:

1. Production

* The node added to the Admin Bar has a red background (i.e., Stop/be careful with any changes you make).
* If neither a staging, QA, nor development environment is detected, then `Prod` is displayed in the Admin Bar.

2. Development

* The node added to the Admin Bar has a green background (i.e., Go ahead, it is safe to make changes).
* If WP is running on localhost (either a loopback IP address or LAN IP address), then `Local` displays in the Admin Bar.
* If WP_DEBUG is defined & true, then `Dev` displays in the Admin Bar.

== Customizing the out-of-the-box behavior ==

Two additional environments are supported, but cannot be automatically detected:

3. Staging

* The node added to the Admin Bar has a yellow-ish background (i.e., Slow down, changes _might_ make it into the production site).
* For information on how to enable this environment, see the [documentation](https://github.com/pbiron/shc-show-env/).

4. QA

* The node added to the Admin Bar has a blue background (i.e., "It's cool", changes you make won't affect the production site).
* For information on how to enable this environment, see the [documentation](https://github.com/pbiron/shc-show-env/).

== Screenshots ==

1. Production
2. Staging
3. QA
4. Development
5. Custom &mdash; Preview

== Changelog ==

= 1.1 =

* General code reorg
* Added support for QA environment
* minor CSS fixes
* changed the Text Domain (for localization) to 'show-environment-in-admin-bar', so that the [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/show-environment-in-admin-bar) service can be used.

= 1.0.1 =

* Correct formatting in readme.txt
* Removed GitHub Plugin URL reader

= 1.0 =

* Initial release on .org
