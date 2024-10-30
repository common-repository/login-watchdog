<?php
/**
 * Plugin Name: Login Watchdog
 * Plugin URI: https://wordpress.org/plugins/login-watchdog
 * Description: Plugin records failed login attempts and in case of exceeding the setting number of failed attempts blocks all login attempts from that IP address. In the administration shows a list of failed records with detailed overview (username, IP address, geolocation data).
 * Version: 1.0.4
 * Author: James JkmAS Mejstrik
 * Author URI: https://jkmas.cz
 * License: GNU GPLv3
 * Text Domain: login-watchdog
 */
 
/*
	Login Watchdog
	Copyright (C) 2016 James JkmAS Mejstrik (email : jkmasg@gmail.com)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/ 
 
//blocking direct access to plugin 
defined('ABSPATH') or die('Blocking direct access to plugin, use WP admin instead.');

//load languages
load_plugin_textdomain('login-watchdog', false, dirname(plugin_basename(__FILE__)).'/languages/');

//hooks for installation, deactivation and uninstall of plugin
register_activation_hook(__FILE__, array('WatchdogInstallation','activate'));	
register_deactivation_hook(__FILE__, array('WatchdogInstallation','deactivate'));
register_uninstall_hook(__FILE__, array('WatchdogInstallation','uninstall'));

//load the necessary files
require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'WatchdogControl.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'WatchdogIpService.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'WatchdogUserAgentService.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'WatchdogAdministration.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'WatchdogInstallation.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'WatchdogValidation.php';

/**
 * WatchdogSetup
 * 
 * Class initializes the plugin 
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogSetup {
	
	const WATCHDOG_VERSION = '1.0.0';
	const WATCHDOG_DATABASE_PREFIX = 'watchdog_';
	
	/**
	 * Initialization of plugin
	 * @access public
	 */
    public static function init() {		
		new WatchdogInstallation();
		new WatchdogControl();
		new WatchdogAdministration();
    }
}
WatchdogSetup::init();
