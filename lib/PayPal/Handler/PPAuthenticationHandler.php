<?php
namespace PayPal\Handler;
use PayPal\Auth\PPSignatureCredential;
use PayPal\Auth\PPCertificateCredential;
use PayPal\Handler\IPPHandler;
use PayPal\Handler\PPSignatureAuthHandler;
use PayPal\Handler\PPCertificateAuthHandler;
use PayPal\Exception\PPInvalidCredentialException;

class PPAuthenticationHandler implements IPPHandler {	
	
	public function handle($httpConfig, $request, $options) {
		$credential = $request->getCredential();
		if(isset($credential)) {
			if($credential instanceof PPSignatureCredential) {
				$handler = new PPSignatureAuthHandler($credential);
			} else if($credential instanceof PPCertificateCredential) {
				$handler = new PPCertificateAuthHandler($credential);
			} else {
				throw new PPInvalidCredentialException();
			}
			$handler->handle($httpConfig, $request, $options);
		}
	}
}