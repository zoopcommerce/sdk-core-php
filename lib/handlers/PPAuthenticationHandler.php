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
	
	public function handle($httpConfig) {
		if($this->apiCredential instanceof PPSignatureCredential) {
			$handler = new PPSignatureAuthHandler($this->apiCredential);
		} else if($this->apiCredential instanceof PPCertificateCredential) {
			$handler = new PPCredentialAuthHandler($this->apiCredential);
		} else {
			throw new PPInvalidCredentialException();
		}
		$handler->handle($httpConfig);
	}
	
	public function appendSoapHeader($payLoad, $apiCred,  $connection,  $accessToken = null, $tokenSecret = null ,$url = null)
	{
		$soapHeader = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ns=\"urn:ebay:api:PayPalAPI\" xmlns:ebl=\"urn:ebay:apis:eBLBaseComponents\" xmlns:cc=\"urn:ebay:apis:CoreComponentTypes\" xmlns:ed=\"urn:ebay:apis:EnhancedDataTypes\">";

		if(isset($accessToken)&& isset($tokenSecret))
		{
			$soapHeader .= "<soapenv:Header>";
			$soapHeader .="<ns:RequesterCredentials/>";
			$soapHeader .="</soapenv:Header>";
		}
		else if($apiCred instanceof PPSignatureCredential)
		{
			$soapHeader .="<soapenv:Header>";
			$soapHeader .="<ns:RequesterCredentials>";
			$soapHeader .="<ebl:Credentials>";
			$soapHeader .="<ebl:Username>".$apiCred->getUserName()."</ebl:Username>";
			$soapHeader .="<ebl:Password>". $apiCred->getPassword()."</ebl:Password>";
			$soapHeader .="<ebl:Signature>".$apiCred->getSignature()."</ebl:Signature>";
			$subject = $apiCred->getSubject();
			if(isset($subject) && $subject != "")
			{
				$soapHeader .="<ebl:Subject>".$apiCred->getSubject()."</ebl:Subject>";
			}
			$soapHeader .="</ebl:Credentials>";
			$soapHeader .="</ns:RequesterCredentials>";
			$soapHeader .="</soapenv:Header>";
		}
		else if($apiCred instanceof PPCertificateCredential)
		{
			$soapHeader .="<soapenv:Header>";
			$soapHeader .="<ns:RequesterCredentials>";
			$soapHeader .="<ebl:Credentials>";
			$soapHeader .="<ebl:Username>".$apiCred->getUserName()."</ebl:Username>";
			$soapHeader .="<ebl:Password>". $apiCred->getPassword()."</ebl:Password>";
			$subject = $apiCred->getSubject();
			if(isset($subject) && $subject != "")
			{
				$soapHeader .="<ebl:Subject>".$apiCred->getSubject()."</ebl:Subject>";
			}
			$soapHeader .="</ebl:Credentials>";
			$soapHeader .="</ns:RequesterCredentials>";
			$soapHeader .="</soapenv:Header>";
            $connection->setSSLCert($apiCred->getCertificatePath(), $apiCred->getPassPhrase());
		}
		$soapHeader .="<soapenv:Body>";
		$soapHeader .= $payLoad;
		$soapHeader .="</soapenv:Body>";
		$soapHeader .="</soapenv:Envelope>";
        return $soapHeader;

	}

}

?>