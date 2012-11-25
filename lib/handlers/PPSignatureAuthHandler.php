<?php
require_once 'IPPHandler.php';

class PPSignatureAuthHandler implements IPPHandler {
	
	private $apiCredential;
	/**
	 *
	 * @param IPPCredential $apiCredential
	 */
	public function __construct($apiCredential) {
		$this->apiCredential = $apiCredential;
	}
	
	public function handle($httpConfig) {
		$thirdPartyAuth = $this->apiCredential->getThirdPartyAuthorization();
		if($thirdPartyAuth && $thirdPartyAuth instanceof PPTokenAuthorization) {
			$httpConfig->addHeader('X-PAYPAL-AUTHORIZATION', AuthSignature::generateFullAuthString($apiCred, $accessToken, $tokenSecret, $httpConfig->getUrl()));
		} else {
			$httpConfig->addHeader('X-PAYPAL-SECURITY-USERID', $this->apiCredential->getUserName());
			$httpConfig->addHeader('X-PAYPAL-SECURITY-PASSWORD', $this->apiCredential->getPassword());
			$httpConfig->addHeader('X-PAYPAL-SECURITY-SIGNATURE', $this->apiCredential->getSignature());
			if($thirdPartyAuth) {
				$httpConfig->addHeader('X-PAYPAL-SECURITY-SUBJECT', $thirdPartyAuth->getSubject());
			}
		}
	}
	
}