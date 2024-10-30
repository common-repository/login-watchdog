<?php
//blocking direct access to plugin 
defined('ABSPATH') or die(); 

/**
 * WatchdogControl
 * 
 * Class records failed login attempts
 * and according to the admin setting blocking individual IP addresses
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogControl{
		
	//user IP address
	private $userIpAddress = null;
	
	//wordpress database
	private $wpdb;
	
	//options	
	private $watchdogLoginAttemps;
	private $watchdogLoginTimeLockdown;
	
	//whole table name of blocked_ip table
	private $tableBlockedIp;
	
	
	/**
	 * Constructor
	 * Set filters and actions, initialization
	 * @access public
	 */
	public function __construct(){ 
		
		//for WP query
		global $wpdb;
		$this->wpdb = $wpdb;
		
		//set whole table name of blocked_ip
		$this->tableBlockedIp = WatchdogSetup::WATCHDOG_DATABASE_PREFIX.'blocked_ip';
		
		//get IP address of user
		$ipService = new WatchdogIpService();
		$this->userIpAddress = $ipService->__getIPAddress();
		
		//get options or set default values
		$this->watchdogLoginAttemps = get_option('WATCHDOG_LOGIN_ATTEMPS', 3);
		$this->watchdogLoginTimeLockdown = get_option('WATCHDOG_LOGIN_TIME_LOCKDOWN', "30");
		
		//add filter to authentication for check if the ip address is not blocked
		add_filter('authenticate', array($this,'checkIpAddress'), 100, 2);
		//add action to wp_login_failed to record ip address and other information
		add_action('wp_login_failed',array($this,'registerLoginFailure'), 10, 1); 
	}
 
	/**
	 * Check if IP address of user is not blocked
	 * 
	 * @param $user WP_User 
	 * @param $username Username
	 * 
	 * @access public
	 * @return WP_User|WP_Error User on success, error on failure
	 */
	public function checkIpAddress($user, $username) {	
		
		//if username is empty, do not check ip address
		if($username == ""){
			return $user;
		}
				
		//find record with the same ip address
		$result = $this->wpdb->get_row($this->wpdb->prepare( 
					   "SELECT counter, time_attempt FROM {$this->tableBlockedIp} WHERE ip_address = %s", $this->userIpAddress
				  ));	
					  			   
		//if IP address is recorded in the database
		if($result !== null){
			//add login time lockdown (in minutes)
			$result->time_attempt += $this->watchdogLoginTimeLockdown*60;
			
			//if is not possible to identify the ip address
			if($this->userIpAddress === null){
				return new WP_Error('broke', __("Your IP address can not be identified, for a successful login you can not hide your ip address",
												"login-watchdog"));
			}
			
			//if the address is blocked
			if($result->time_attempt >= time() && $result->counter >= $this->watchdogLoginAttemps){
				return new WP_Error('broke', __("Logging in from your IP address is blocked, try it later",
												"login-watchdog"));
			} else {
				return $user;
			}
		} else {
			return $user;
		}
	}
	
	/**
	 * Register user login failure and record his ip address and count of failed attemps	
	 * @param $username Username from the login form 
	 * @access private 
	 */
	public function registerLoginFailure($username) {
		//find record with the same ip address
		$record = $this->wpdb->get_row($this->wpdb->prepare( 
					   "SELECT time_attempt, counter FROM {$this->tableBlockedIp} WHERE ip_address = %s", 
						$this->userIpAddress
				  ));	
		
		//set counter to default value
		$counter = 1;
							   
	    //if record exists - update
		if($record !== null){
			//add login time lockdown (in minutes)
			$record->time_attempt += $this->watchdogLoginTimeLockdown*60;
									
			//if still valid timestamp, increase counter
			if($record->time_attempt >= time()){
				$record->counter++;
				$counter = $record->counter;
			} 			
		}	
		
		$userAgentService = new WatchdogUserAgentService();					
		
		//update or insert the record		
		$this->wpdb->replace($this->tableBlockedIp, 
			array( 
				'ip_address' => $this->userIpAddress,
				'os' => $userAgentService->__getOS(),
				'user_agent_info' => $userAgentService->__getUserAgent(),
				'time_attempt' => time(), 
				'counter' => $counter,
				'username' => (!empty($username) ? $username : 'unknown'),
			),array('%s','%s','%s','%s','%d','%s'));	
	}
}
