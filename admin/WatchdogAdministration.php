<?php
//blocking direct access to plugin 
defined('ABSPATH') or die();

/**
 * WatchdogAdministration
 * 
 * Administration of plugin
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogAdministration{
	
	//records from database
	private $records = [];
	
	//count of records in database
	private $recordsCount;
	
	//displayed records;
	private $displayedRecordsCount;
	
	//options
	private $watchdogLoginAttemps;
	private $watchdogLoginTimeLockdown;
	private $watchdogRecordsLimit;
	
	//whole table name of blocked_ip table
	private $tableBlockedIp;
	
	//wordpress database
	private $wpdb;
	
	/**
	 * Constructor
	 * Add actions, initialization
	 * @access public
	 */
	public function __construct(){
		//checks if the administration panel is attempting to be displayed	
		if (is_admin()){
			
			//get options or set default values 			
			$this->watchdogLoginAttemps = get_option('WATCHDOG_LOGIN_ATTEMPS', 3);
			$this->watchdogLoginTimeLockdown = get_option('WATCHDOG_LOGIN_TIME_LOCKDOWN', "30");
			$this->watchdogRecordsLimit = get_option('WATCHDOG_RECORDS_LIMIT', 30);  
			
			//for WP query
			global $wpdb;
			$this->wpdb = $wpdb;
			
			//set whole table name of blocked_ip
			$this->tableBlockedIp = WatchdogSetup::WATCHDOG_DATABASE_PREFIX.'blocked_ip';
			
			//if admin wants to delete one record
			if (isset($_GET['action'])){
				if($_GET['action'] == 'trash' && isset($_GET['ip'])){
					$this->deleteRecord($_GET['ip']);
				}				
			}
			
			//if admin wants to delete all records
			if(isset($_POST['deleteAllRecords'])){
				$this->deleteRecords();
			}
			
			//add action to ajax call for saving geo information
			add_action('wp_ajax_save_geo_ip', array('WatchdogIpService', 'saveGeoIpInformation'));
			
			//add actions, init administration and get data from database
			add_action('admin_menu', array($this, 'pluginPage'));
			add_action('admin_init', array($this, 'init'));			
			$this->__getData();
		}		
	}
	
	/**
	 * Set plugin administration page and init HTML and JS of plugin
	 *  
	 * @access public
	 */
	public function pluginPage() {	
		//add plugin to settings menu
		add_options_page('Login Watchdog', 
						 'Login Watchdog', 
						 'manage_options', 
						 'login-watchdog', 
						  array($this,'initHtml'));
		//registr plugin JS
		add_action('admin_enqueue_scripts', array($this,'initJs'));
	}
	
	/**
	 * Initialization for settings of options and set callback validation functions
	 *  
	 * @access public
	 */
	public function init(){
		register_setting('login-watchdog', 'WATCHDOG_LOGIN_ATTEMPS', array('WatchdogValidation', 'validateLoginAttemps'));
		register_setting('login-watchdog', 'WATCHDOG_LOGIN_TIME_LOCKDOWN', array('WatchdogValidation', 'validateLoginLockdown'));
		register_setting('login-watchdog', 'WATCHDOG_RECORDS_LIMIT', array('WatchdogValidation', 'validateRecordsLimit'));
	}
	
	/**
	 * Initialization of plugin´s HTML
	 *  
	 * @access public
	 */	
	public function initHtml(){ 
		require_once __DIR__.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'administration.php';		
	}
	
	/**
	 * Initialization of plugin´s JavaScript
	 *  
	 * @access public
	 * @param $hook Hook 
	 */
	public function initJs($hook) {
		// Only applies to plugin page
		if('settings_page_login-watchdog' != $hook) {			
			return;
		}	
		
		//print the JS		
		wp_enqueue_script('watchdog-geoip-script', plugins_url( '/assets/js/geoip.js', __FILE__ ), array('jquery')); 
		
		//set the parameters using in JS
		$params = array(
			// Get the url to the admin-ajax.php file using admin_url()
			'ajaxUrl' => admin_url('admin-ajax.php', (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://')
		);		
		//print parameters 
		wp_localize_script('watchdog-geoip-script', 'params', $params);
	}
	
	/**
	 * Get records and other data from database
	 *  
	 * @access public
	 */
	private function __getData(){
		//get records
		$result = $this->wpdb->get_results($this->wpdb->prepare( 
					   "SELECT * FROM {$this->tableBlockedIp} ORDER BY time_attempt DESC LIMIT %d", $this->watchdogRecordsLimit
				  ));
		$this->records = $result;
		
		//get count of records
		$count = $this->wpdb->get_var($this->wpdb->prepare( 
					   "SELECT COUNT(*) FROM {$this->tableBlockedIp}", null
				  ));
		$this->recordsCount = $count;
		
		//get count of displayed records
		$this->displayedRecordsCount = count($result);
		
	}
	
	/**
	 * Delete selected record
	 *  
	 * @access public
	 * @param $ipAddress IP Address of record
	 */
	private function deleteRecord($ipAddress){
		//if ip address is valid
		if(filter_var($ipAddress,FILTER_VALIDATE_IP)){
			$this->wpdb->query($this->wpdb->prepare( 
				"DELETE FROM {$this->tableBlockedIp} WHERE ip_address = %s", $ipAddress
			));
		}
	}
	
	/**
	 * Delete all records
	 *  
	 * @access public
	 */
	private function deleteRecords(){
		$this->wpdb->query($this->wpdb->prepare( 
			"DELETE FROM {$this->tableBlockedIp} ", null
		));
	}
}
