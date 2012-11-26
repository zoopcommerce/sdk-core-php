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
	
	public function handle($httpConfig, $request) {
		$thirdPartyAuth = $this->apiCredential->getThirdPartyAuthorization();
		
		switch($request->getBindingType()) {
			case 'NV':
				if($thirdPartyAuth && $thirdPartyAuth instanceof PPTokenAuthorization) {
					$httpConfig->addHeader('X-PAYPAL-AUTHORIZATION', AuthSignature::generateFullAuthString($this->apiCredential, $accessToken, $tokenSecret, $httpConfig->getUrl()));
				} else {
					$httpConfig->addHeader('X-PAYPAL-SECURITY-USERID', $this->apiCredential->getUserName());
					$httpConfig->addHeader('X-PAYPAL-SECURITY-PASSWORD', $this->apiCredential->getPassword());
					$httpConfig->addHeader('X-PAYPAL-SECURITY-SIGNATURE', $this->apiCredential->getSignature());
					if($thirdPartyAuth) {
						$httpConfig->addHeader('X-PAYPAL-SECURITY-SUBJECT', $thirdPartyAuth->getSubject());
					}
				}
				break;
			case 'SOAP':
				if($thirdPartyAuth && $thirdPartyAuth instanceof PPTokenAuthorization) {
					$httpConfig->addHeader('X-PAYPAL-AUTHORIZATION', AuthSignature::generateFullAuthString($this->apiCredential, $accessToken, $tokenSecret, $httpConfig->getUrl()));
					$request->addBindingInfo('securityHeader' , '<ns:RequesterCredentials/>');
				} else {
					$securityHeader = '<ns:RequesterCredentials><ebl:Credentials>';
					$securityHeader .= '<ebl:Username>' . $this->apiCredential->getUserName() . '</ebl:Username>';
					$securityHeader .= '<ebl:Password>' . $this->apiCredential->getPassword() . '</ebl:Password>';
					$securityHeader .= '<ebl:Signature>' . $this->apiCredential->getSignature() . '</ebl:Signature>';					
					if($thirdPartyAuth && $thirdPartyAuth instanceof PPSubjectAuthorization) {
						$securityHeader .= '<ebl:Subject>' . $thirdPartyAuth->getSubject() . '</ebl:Subject>';
					}
					$securityHeader .= '</ebl:Credentials></ns:RequesterCredentials>';
					$request->addBindingInfo('securityHeader' , $securityHeader);					
				}
				break;
		}
	}
	
}