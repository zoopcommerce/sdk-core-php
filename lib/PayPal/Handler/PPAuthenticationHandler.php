<?php
namespace PayPal\Handler;
use PayPal\Handler\IPPHandler;
use PayPal\Handler\PPSignatureAuthHandler;
use PayPal\Handler\PPCredentialAuthHandler;
use PayPal\Exception\PPInvalidCredentialException;
use PayPal\Auth\PPSignatureCredential;
use PayPal\Auth\PPCertificateCredential;
class PPAuthenticationHandler implements IPPHandler {	
	
	public function handle($httpConfig, $request, $options) {
		$credential = $request->getCredential();
		if(isset($credential)) {
			if($credential instanceof PPSignatureCredential) {
				$handler = new PPSignatureAuthHandler($credential);
			} else if($credential instanceof PPCertificateCredential) {
				$handler = new PPCredentialAuthHandler($credential);
			} else {
				throw new PPInvalidCredentialException();
			}
			$handler->handle($httpConfig, $request, $options);
		}
	}
}