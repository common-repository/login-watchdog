<?php 
//blocking direct access to plugin 
defined('ABSPATH') or die();

/**
 * WatchdogIpService
 * 
 * Service provider working with ip address
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogIpService{
	
	//IP address of user
	private $remote;
		
	/**
	 * Constructor
	 * Initialization
	 * @access public
	 */
	public function __construct(){
		//set user ip address
		$this->remote = $_SERVER["REMOTE_ADDR"];
	}
	
	/**
	 * Save geo information of ip address from admin AJAX call
	 *  
	 * @access public
	 */
	public function saveGeoIpInformation(){
		//for WP query
		global $wpdb;
		
		//set whole table name of blocked_ip
		$tableBlockedIp = WatchdogSetup::WATCHDOG_DATABASE_PREFIX.'blocked_ip';
			
		//find record with the same ip address			
		$record = $wpdb->get_row($wpdb->prepare( 
					   "SELECT * FROM {$tableBlockedIp} WHERE ip_address = %s", 
						$_POST['data']['ip']
				  ));	
				  
	    //if record exists 
		if($record !== null){
			//update record
			$wpdb->replace($tableBlockedIp, 
					array( 
						'ip_address' => $record->ip_address,
						'os' => $record->os,
						'user_agent_info' => $record->user_agent_info,
						'time_attempt' => $record->time_attempt, 
						'counter' => $record->counter,
						'username' => $record->username,
						'country_name' => $_POST['data']['country_name'],
						'region_name' => $_POST['data']['region_name'],
						'latitude' => $_POST['data']['latitude'],
						'longitude' => $_POST['data']['longitude']
					),array('%s','%s','%s','%s','%d','%s','%s','%s','%s','%s'));	
		}
		
		//die
		wp_die();
	}
	
	/**
	 * Get IP address of user 
	 * 
	 * REMOTE_ADDR is the only really reliable information and 
	 * still represents the most reliable source of an IP address. 
	 * 
	 * @access public
	 * @return string|null IPAddress of user or null
	 */
	public function __getIPAddress(){
				
		//can not by empty or invalid format
		if(!empty($this->remote) && filter_var($this->remote,FILTER_VALIDATE_IP)){
			$IPAddress = $this->remote;
		} else {
			$IPAddress = null;
		}
		
		return $IPAddress;
	}
}
