<?php
namespace PayPal\Rest;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Handler\IPPHandler;
use PayPal\Core\PPCredentialManager;
use PayPal\Core\PPConstants;
use PayPal\Exception\PPMissingCredentialException;
use PayPal\Exception\PPInvalidCredentialException;
use PayPal\Exception\PPConfigurationException;
use PayPal\Common\PPUserAgent;

/**
 * 
 * API handler for all REST API calls
 */
class RestHandler implements IPPHandler {

	private $sdkName;	
	private $sdkVersion;
	
	/**
	 * @param string $sdkName
	 * @param string $sdkVersion
	 */
	public function __construct($sdkName='rest-sdk-php', $sdkVersion='') {
		$this->sdkName = $sdkName;
		$this->sdkVersion = $sdkVersion;
	}
	
	public function handle($httpConfig, $request, $options) {
	
		$apiContext = $options['apiContext'];	
		$credential = $apiContext->getCredential();
		$config = $apiContext->getConfig();
		
		if($credential == NULL) {
			// Try picking credentials from the config file
			$credMgr = PPCredentialManager::getInstance($config);
			$credValues = $credMgr->getCredentialObject();
			if(!is_array($credValues)) {
				throw new PPMissingCredentialException("Empty or invalid credentials passed");
			}
			$credential = new OAuthTokenCredential($credValues['clientId'], $credValues['clientSecret']);
		}
		if($credential == NULL || ! ($credential instanceof OAuthTokenCredential) ) {
			throw new PPInvalidCredentialException("Invalid credentials passed");
		}

		
		$httpConfig->setUrl(
			rtrim( trim($this->_getEndpoint($config)), '/') . 
				(isset($options['path']) ? $options['path'] : '')
		);
		
		if(!array_key_exists("User-Agent", $httpConfig->getHeaders())) {
			$httpConfig->addHeader("User-Agent", PPUserAgent::getValue($this->sdkName, $this->sdkVersion));
		}		
		if(!is_null($credential) && $credential instanceof OAuthTokenCredential) {
			$httpConfig->addHeader('Authorization', "Bearer " . $credential->getAccessToken($config));
		}
		if($httpConfig->getMethod() == 'POST' || $httpConfig->getMethod() == 'PUT') {
			$httpConfig->addHeader('PayPal-Request-Id', $apiContext->getRequestId());
		}
		
	}
	
	private function _getEndpoint($config) {
		if (isset($config['service.EndPoint'])) {
			return $config['service.EndPoint'];
		} else if (isset($config['mode'])) {
			switch (strtoupper($config['mode'])) {
				case 'SANDBOX':
					return PPConstants::REST_SANDBOX_ENDPOINT;
					break;
				case 'LIVE':
					return PPConstants::REST_LIVE_ENDPOINT;
					break;
				default:
					throw new PPConfigurationException('The mode config parameter must be set to either sandbox/live');
					break;
			}
		} else {
			throw new PPConfigurationException('You must set one of service.endpoint or mode parameters in your configuration');
		}
	}

}
