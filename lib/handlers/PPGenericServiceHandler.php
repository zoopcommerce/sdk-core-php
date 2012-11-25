<?php

require_once 'IPPHandler.php';

class PPGenericServiceHandler implements IPPHandler {

	public function handle($httpConfig) {		
		$config = PPConfigManager::getInstance();
		$httpConfig->addHeader('X-PAYPAL-REQUEST-DATA-FORMAT', $config->get('service.Binding'));
		$httpConfig->addHeader('X-PAYPAL-RESPONSE-DATA-FORMAT', $config->get('service.Binding'));
		$httpConfig->addHeader('X-PAYPAL-DEVICE-IPADDRESS', PPUtils::getLocalIPAddress());
		$httpConfig->addHeader('X-PAYPAL-REQUEST-SOURCE', PPUtils::getRequestSource());		
	}
}