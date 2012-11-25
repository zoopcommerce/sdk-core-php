<?php
require_once 'IPPHandler.php';

class PPCredentialAuthHandler implements IPPHandler {

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
			$httpConfig->addHeader('X-PAYPAL-AUTHORIZATION', 
					AuthSignature::generateFullAuthString($this->apiCredential, $thirdPartyAuth->getAccessToken(), $thirdPartyAuth->getTokenSecret(), $httpConfig->getUrl()));
		} else {
			$httpConfig->addHeader('X-PAYPAL-SECURITY-USERID', $this->apiCredential->getUserName());
			$httpConfig->addHeader('X-PAYPAL-SECURITY-PASSWORD', $this->apiCredential->getPassword());
			$httpConfig->setSSLCert($this->apiCredential->getCertificatePath(), $this->apiCredential->getCertificatePassPhrase());
			if($thirdPartyAuth) {
				$httpConfig->addHeader('X-PAYPAL-SECURITY-SUBJECT', $thirdPartyAuth->getSubject());
			}
		}
	}

}