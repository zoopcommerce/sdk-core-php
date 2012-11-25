<?php

require_once 'PPGenericServiceHandler.php';

class PPMerchantServiceHandler extends PPGenericServiceHandler {

	public function handle($httpConfig) {
		parent::handle($httpConfig);
		if($httpConfig->getHeader('X-PAYPAL-AUTHORIZATION')) {
			$httpConfig->addHeader('X-PP-AUTHORIZATION', $httpConfig->getHeader('X-PAYPAL-AUTHORIZATION'));
			$httpConfig->removeHeader('X-PAYPAL-AUTHORIZATION');
		}
	}
}