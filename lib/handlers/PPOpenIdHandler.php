<?php

class PPOpenIdHandler implements IPPHandler {
	
	private $config;
	
	private static $sdkName = "openid-sdk-php";	
	private static $sdkVersion = "1.0.0";
	
	public function __construct($config) {
		$this->config = $config;
	}

	public function handle($httpConfig, $request, $options) {

		if (isset($this->config['openid.EndPoint'])) {
			$endpoint = $this->config['openid.EndPoint'];
		} else if (isset($this->config['service.EndPoint'])) {
			$endpoint = $this->config['service.EndPoint'];
		} else if (isset($this->config['mode'])) {
			switch (strtoupper($this->config['mode'])) {
				case 'SANDBOX':
					$endpoint = PPConstants::REST_SANDBOX_ENDPOINT;
					break;
				case 'LIVE':
					$endpoint = PPConstants::REST_LIVE_ENDPOINT;
					break;
				default:
					throw new PPConfigurationException('The mode config parameter must be set to either sandbox/live');
					break;
			}
		} else {
			throw new PPConfigurationException('You must set one of service.endpoint or mode parameters in your configuration');
		}

		$httpConfig->setUrl(
			rtrim( trim($endpoint), '/') . (isset($options['path']) ? $options['path'] : '')
		);
		if(!array_key_exists("Authorization", $httpConfig->getHeaders())) {			
			$auth = base64_encode($this->config['acct1.ClientId'] . ':' . $this->config['acct1.ClientSecret']);
			$httpConfig->addHeader("Authorization", "Basic $auth");
		}
		if(!array_key_exists("User-Agent", $httpConfig->getHeaders())) {
			$httpConfig->addHeader("User-Agent", PPUserAgent::getValue(self::$sdkName, self::$sdkVersion));
		}
	}
}