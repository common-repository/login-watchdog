<?php 
//blocking direct access to plugin 
defined('ABSPATH') or die();

/**
 * WatchdogUserAgentService
 * 
 * Service associated with browser and OS
 *
 * @author jkmas <jkmasg@gmail.com>
 * @version 1.0.4
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @access public
 */
class WatchdogUserAgentService{
	
	//user agent information
	private $userAgent;
		
	/**
	 * Constructor
	 * Initialization
	 * @access public
	 */
	public function __construct(){
		//set user agent information
		if(isset($_SERVER["HTTP_USER_AGENT"])){
			$this->userAgent = $_SERVER["HTTP_USER_AGENT"];
		} else {
			$this->userAgent = null;
		}
	}
	
	/**
	 * Get operating system of user 
	 * 
	 * @access public
	 * @return string Operating system or unknown
	 */
	public function __getOS(){
                //android must be before linux (it containts also linux)
                if(preg_match('/android/i',$this->userAgent)){
			return 'Android';
		} elseif(preg_match('/linux/i',$this->userAgent)){
			return 'Linux';
		} elseif(preg_match('/win/i',$this->userAgent)){
			return 'Windows';
                //iphone,ipod,ipad must be before mac (it containts also mac)
                } elseif(preg_match('/iphone/i',$this->userAgent)){
			return 'iPhone';		
                } elseif(preg_match('/ipod/i',$this->userAgent)){
			return 'iPod';
		} elseif(preg_match('/ipad/i',$this->userAgent)){
			return 'iPad';
		} elseif(preg_match('/mac/i',$this->userAgent)){
			return 'MacOS';
		} elseif(preg_match('/blackberry/i',$this->userAgent)){
			return 'BlackBerry';		
		} else {
			return 'Unknown';
		}
	}
	
	/**
	 * Get user agent string
	 * 
	 * @access public
	 * @return string User agent information
	 */
	public function __getUserAgent(){
		return $this->userAgent;
	}
}
