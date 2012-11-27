<?php
require_once 'IPPHandler.php';

class PPCredentialAuthHandler implements IPPHandler {

	public function handle($httpConfig, $request) {

		$credential = $request->getCredential();
		if(!isset($credential)) {
			return;
		}
		$thirdPartyAuth = $credential->getThirdPartyAuthorization();
		$httpConfig->setSSLCert($credential->getCertificatePath(), $credential->getCertificatePassPhrase());
		
		switch($request->getBindingType()) {
			case 'NV':
				if($thirdPartyAuth && $thirdPartyAuth instanceof PPTokenAuthorization) {
					$httpConfig->addHeader('X-PAYPAL-AUTHORIZATION', 
							AuthSignature::generateFullAuthString($credential, $thirdPartyAuth->getAccessToken(), $thirdPartyAuth->getTokenSecret(), $httpConfig->getUrl()));
				} else {
					$httpConfig->addHeader('X-PAYPAL-SECURITY-USERID', $credential->getUserName());
					$httpConfig->addHeader('X-PAYPAL-SECURITY-PASSWORD', $credential->getPassword());					
					if($thirdPartyAuth) {
						$httpConfig->addHeader('X-PAYPAL-SECURITY-SUBJECT', $thirdPartyAuth->getSubject());
					}
				}
				break;
			case 'SOAP':
				if($thirdPartyAuth && $thirdPartyAuth instanceof PPTokenAuthorization) {
					$httpConfig->addHeader('X-PAYPAL-AUTHORIZATION', AuthSignature::generateFullAuthString($credential, $accessToken, $tokenSecret, $httpConfig->getUrl()));
					$request->addBindingInfo('securityHeader' , '<ns:RequesterCredentials/>');
				} else {
					$securityHeader = '<ns:RequesterCredentials><ebl:Credentials>';
					$securityHeader .= '<ebl:Username>' . $credential->getUserName() . '</ebl:Username>';
					$securityHeader .= '<ebl:Password>' . $credential->getPassword() . '</ebl:Password>';					
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