<?php
require_once 'PPConfigManager.php';
require_once 'PPConnectionManager.php';
require_once 'PPHttpConfig.php';
/**
 * 
 *
 */
class PPIPNMessage {
	
	const IPN_CMD = 'cmd=_notify-validate';
	
	/**
	 * 
	 * @var array
	 */
	private $ipnData;

	/**
	 * 
	 * @param array $postData OPTIONAL post data. If null, 
	 * 				the class automatically reads incoming POST data 
	 * 				from the input stream
	 */
	public function __construct($postData=null) {
		if(is_null($postData)) {
			
			// reading posted data from directly from $_POST may causes serialization issues with array data in POST
			// reading raw POST data from input stream instead.			
			$rawPostData = file_get_contents('php://input');
			$rawPostArray = explode('&', $rawPostData);
			$postData = array();
			foreach ($rawPostArray as $keyValue) {
				$keyValue = explode ('=', $keyValue);
				if (count($keyValue) == 2)
					$postData[$keyValue[0]] = urldecode($keyValue[1]);
			}
		}
		//TODO: If postData is passed in, should we urldecode values??
		$this->ipnData = $postData;	
		

		$fh = fopen('log.txt', 'a') or die("Can't open file.");
		$results = print_r($this->ipnData, true);
		fwrite($fh, $results);
		fclose($fh);
	}
	
	/**
	 * Returns a hashmap of raw IPN data
	 * 
	 * @return array  
	 */
	public function getRawData() {
		return $this->ipnData;
	}
	
	/**
	 * Validates a IPN message
	 * 
	 * @return boolean
	 */
	public function validate() {
		$request = self::IPN_CMD;
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($this->ipnData as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$request .= "&$key=$value";

		}
		$config = PPConfigManager::getInstance();
		$httpConfig = new PPHttpConfig($config->get('service.IPNEndpoint'), PPHttpConfig::HTTP_POST);
		$httpConfig->addCurlOption('CURLOPT_FORBID_REUSE', 1);
	    $httpConfig->addCurlOption('CURLOPT_HTTPHEADER', array('Connection: Close'));
		$connection = PPConnectionManager::getInstance()->getConnection($httpConfig);
		$response = $connection->execute($request);

		if($response == 'VERIFIED')
			return true;
		else if ($response == 'INVALID')
			return false;
	}
	
	/**
	 * Returns the transaction id for which
	 * this IPN was generated, if one is available
	 *
	 * @return string
	 */
	public function getTransactionId() {
		
	}
	
	/**
	 * Returns the transaction type for which
	 * this IPN was generated
	 * 
	 * @return string
	 */
	public function getTransactionType() {
		return $this->ipnData['transaction_type'];
	}	
	
}