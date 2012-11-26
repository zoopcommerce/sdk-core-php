<?php

require_once 'PPGenericServiceHandler.php';

class PPPlatformServiceHandler extends PPGenericServiceHandler {

	private $apiCredential;
	/**
	 *
	 * @param IPPCredential $apiCredential
	 */
	public function __construct($apiCredential) {
		$this->apiCredential = $apiCredential;
	}
	
	public function handle($httpConfig, $request) {
		parent::handle($httpConfig, $request);
		if($this->apiCredential->getApplicationId() != NULL) {
			$httpConfig->addHeader('X-PAYPAL-APPLICATION-ID', $this->apiCredential->getApplicationId());
		}
	}
}