<?php

require_once dirname(__FILE__) . '/../auth/PPSignatureCredential.php';
require_once dirname(__FILE__) . '/../auth/PPCertificateCredential.php';
require_once dirname(__FILE__) . '/../exceptions/PPInvalidCredentialException.php';
require_once 'IPPHandler.php';
require_once 'PPSignatureAuthHandler.php';
require_once 'PPCertificateAuthHandler.php';

class PPAuthenticationHandler implements IPPHandler {
	
	private $apiCredential;
	/**
	 *
	 * @param IPPCredential $apiCredential
	 */
	public function __construct($apiCredential) {
		$this->apiCredential = $apiCredential;
	}
	
	public function handle($httpConfig, $request) {
		if($this->apiCredential instanceof PPSignatureCredential) {
			$handler = new PPSignatureAuthHandler($this->apiCredential);
		} else if($this->apiCredential instanceof PPCertificateCredential) {
			$handler = new PPCredentialAuthHandler($this->apiCredential);
		} else {
			throw new PPInvalidCredentialException();
		}
		$handler->handle($httpConfig, $request);
	}
}

?>