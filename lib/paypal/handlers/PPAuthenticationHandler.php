<?php
namespace paypal\handlers;
use paypal\handlers\IPPHandler;
use paypal\handlers\PPSignatureAuthHandler;
use paypal\handlers\PPCredentialAuthHandler;
use paypal\exceptions\PPInvalidCredentialException;
use paypal\auth\PPSignatureCredential;
use paypal\auth\PPCertificateCredential;
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