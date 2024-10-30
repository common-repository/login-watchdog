<?php 
//blocking direct access to plugin 
defined('ABSPATH') or die();

/**
 * WatchdogValidations
 * 
 * Static functions for validation of data
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogValidation{
	
	//message
	private static $message = null;
	
	//type of message
	private static $type = null;
	
	/**
	 * Validation of the correct setting of login false attemps 
	 * 
	 * @access public
	 * @param $data Post data from form 
	 * @return string Return post data or default data
	 */
	public static function validateLoginAttemps($data){
		
		self::$type = null;
		$default = 3;		
		
		//must be number
		if (empty($data) || filter_var($data, FILTER_VALIDATE_INT) === false) {
			self::$type = 'error';
			self::$message = __("Number of attemps can not be empty and must be a number","login-watchdog");
		} else {
			//must be between 1 and 10
			if($data > 10 || $data < 1){
				self::$type = 'error';
				self::$message = __("Number of attemps must be between 1 and 10","login-watchdog");
			}
		}
		
		//show error or return data
		if(self::$type == 'error'){
			self::showError('login-attemps');		
			return $default;	
		} else {
			return $data;
		}
	}
	
	/**
	 * Validation of the correct setting of blocking
	 * 
	 * @access public
	 * @param $data Post data from form 
	 * @return string Return post data or default data
	 */
	public static function validateLoginLockdown($data){
		
		self::$type = null;
		$default = 30;
		
		//must be number
		if (empty($data) || filter_var($data, FILTER_VALIDATE_INT) === false) {
			self::$type = 'error';
			self::$message = __("Length of blocking IP addresses can not be empty and must be a number","login-watchdog");
		} else {
			//must be between 1 month and 10 minutes
			if($data > 60*24*30 || $data < 10){
				self::$type = 'error';
				self::$message = __("Length of blocking IP addresses must be between 10 mins and 1 month (43200 mins)","login-watchdog");
			}
		}
		
		//show error or return data
		if(self::$type == 'error'){
			self::showError('login-lockdown');
			return $default;
		} else {
			return $data;
		}
	}
	
	/**
	 * Validation of the correct setting of displayed records limit 
	 * 
	 * @access public
	 * @param $data Post data from form 
	 * @return string Return post data or default data
	 */
	public static function validateRecordsLimit($data){
		
		self::$type = null;
		$default = 30;		
		
		//must be number
		if (empty($data) || filter_var($data, FILTER_VALIDATE_INT) === false) {
			self::$type = 'error';
			self::$message = __("Maximum number of displayed records can not be empty and must be a number","login-watchdog");
		} 
		
		//show error or return data
		if(self::$type == 'error'){
			self::showError('records-limit');		
			return $default;	
		} else {
			return $data;
		}
	}
	
	/**
	 * Show error with message
	 * 
	 * @access public
	 * @param $type Type of error
	 */
	public function showError($type){
		add_settings_error(
			'login-watchdog',
			$type,
			self::$message,
			self::$type
		);
	}
}
