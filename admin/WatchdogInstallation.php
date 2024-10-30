<?php
//blocking direct access to plugin 
defined('ABSPATH') or die();

/**
 * WatchdogInstallation
 * 
 * Class handles the installation, deactivation, upgrade and uninstallation 
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.2
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogInstallation{
				
	/**
	 * Function installs the plugin and creates tables
	 * @access public
	 */	
	public static function activate(){	
                //table name of blocked_ip with prefix
                $tableName = WatchdogSetup::WATCHDOG_DATABASE_PREFIX.'blocked_ip';
            
		$sql = "CREATE TABLE IF NOT EXISTS ".$tableName." (
			ip_address VARCHAR(100) NOT NULL,
			os VARCHAR(10) DEFAULT NULL,
			user_agent_info TEXT DEFAULT NULL,
			time_attempt INT(8) NOT NULL,
			username VARCHAR(100) DEFAULT 'unknown',
			counter INT(1),
			country_name VARCHAR(100) DEFAULT NULL,
			region_name VARCHAR(100) DEFAULT NULL,
			latitude VARCHAR(10) DEFAULT NULL,
			longitude VARCHAR(10) DEFAULT NULL,	 
			PRIMARY KEY  (ip_address),
			KEY (ip_address)
		);";		
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		//set version of plugin to database
		update_option('WATCHDOG_LOGIN_VERSION', WatchdogSetup::WATCHDOG_VERSION);
	}
	
	/**
	 * Function uninstalls the plugin - drops tables and deletes options
	 * @access public
	 */	
	public static function uninstall(){
                //table name of blocked_ip with prefix
                $tableName = WatchdogSetup::WATCHDOG_DATABASE_PREFIX.'blocked_ip';
            
		global $wpdb;
		$sql = "DROP TABLE ".$tableName;
                $wpdb->query($sql);	

                delete_option('WATCHDOG_LOGIN_ATTEMPS');
                delete_option('WATCHDOG_LOGIN_TIME_LOCKDOWN');	
                delete_option('WATCHDOG_RECORDS_LIMIT');    
                delete_option('WATCHDOG_LOGIN_VERSION');    
	}
	
	/**
	 * Function for the deactivation of the plugin - only delete records from tables
	 * @access public
	 */		
	public static function deactivate(){
                //table name of blocked_ip with prefix
                $tableName = WatchdogSetup::WATCHDOG_DATABASE_PREFIX.'blocked_ip';
            
		global $wpdb;
		$sql = "DELETE FROM ".$tableName;
                $wpdb->query($sql);
	}
}
