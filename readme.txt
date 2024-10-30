=== Login Watchdog ===
Contributors: jkmas
Plugin Name: Login Watchdog
Version: 1.0.4
Author: James JkmAS Mejstrik
Author URI: https://jkmas.cz/
Tags: login, watchdog, security, blocking, ip address, brute force
Requires at least: 3.6
Stable tag: trunk
Tested up to: 4.8
License: GNU GPLv3

Plugin records failed login attempts 
and in case of exceeding the setting number of failed attempts blocks all login attempts from that IP address.

== Description ==

Plugin records failed login attempts 
and in case of exceeding the setting number of failed attempts blocks all login attempts from that IP address. 

In the administration shows a list of failed records with detailed overview (username, IP address, geolocation data).

== Installation ==

1. Upload the `login-watchdog` plugin to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure your desired settings via the settings page.

== Features ==

* Records failed login attempts
* Blocking the logging in from the recorded IP addresses
* Displays the list of failed attempts with the detailed overview (ip address, nickname, geolocation data)

== Screenshots ==

1. Login Watchdog admin page

== Changelog ==

= 1.0.4 =
* I added device recognition of Android, iPhone, iPad and iPod
* Bug fix associated with the table of records
* Check WP 4.8 support

= 1.0.3 =
* Repair of bug associated with removing one record from database
* Support for WP 4.7

= 1.0.2 =
* Repair of the bug in the installation file

= 1.0.1 =
* Repair of the bug in deleting of the plugin

= 1.0.0 =
* first release

= Author =

Created by [JkmAS Mejstrik](http://www.jkmas.cz)
Software is provided without warranty and the software author cannot be held liable for damages.
