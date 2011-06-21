<?php
require_once 'PPCredentialManager.php';
require_once 'PPConnectionManager.php';
require_once 'PPObjectTransformer.php';
require_once 'PPLoggingManager.php';
require_once 'PPUtils.php';

class PPAPIService
{
	public $endpoint;
	public $serviceName;	
	private $logger;
	
	public function __construct($serviceName = "")
	{
		$this->serviceName = $serviceName;
		$config = PPConfigManager::getInstance();
		$this->endpoint = $config->get('service.EndPoint');
		$this->logger = new PPLoggingManager(__CLASS__);			
	}

	public function setServiceName($serviceName)
	{
		$this->serviceName = $serviceName;
	}

	private function getPayPalHeaders($apiCred, $conf, $connection)
	{
		// Add headers required for service authentication
		if($apiCred instanceof PPSignatureCredential)
		{			
			$headers_arr[] = "X-PAYPAL-SECURITY-USERID:  " . $apiCred->getUserName();
			$headers_arr[] = "X-PAYPAL-SECURITY-PASSWORD: " . $apiCred->getPassword();
			$headers_arr[] = "X-PAYPAL-SECURITY-SIGNATURE: " . $apiCred->getSignature();
		}
		else if($apiCred instanceof PPCertificateCredential)
		{
			
			$headers_arr[] = "X-PAYPAL-SECURITY-USERID:  " . $apiCred->getUserName();
			$headers_arr[] = "X-PAYPAL-SECURITY-PASSWORD: " . $apiCred->getPassword();			
			$connection->setSSLCert($apiCred->getCertificatePath());
		} 
		
		// Add other headers 
		$headers_arr[] = "X-PAYPAL-APPLICATION-ID: " . $apiCred->getApplicationId();
		$headers_arr[] = "X-PAYPAL-REQUEST-DATA-FORMAT: "  . $conf->get('service.Binding');
		$headers_arr[] = "X-PAYPAL-RESPONSE-DATA-FORMAT: "  . $conf->get('service.Binding');		
		$headers_arr[] = "X-PAYPAL-DEVICE-IPADDRESS: " . PPUtils::getLocalIPAddress();
		$headers_arr[] = "X-PAYPAL-REQUEST-SOURCE: " . PPUtils::getRequestSource();
		return $headers_arr;
	}
	
	public function makeRequest($apiMethod, $params, $apiUsername = null)
	{
		
		$config = PPConfigManager::getInstance();
		$connectionMgr = PPConnectionManager::getInstance();
		$connection = $connectionMgr->getConnection();		
		
		$credMgr = PPCredentialManager::getInstance();
		// $apiUsernam is optional, if null the default account in congif file is taken
		$apIPPCredential = $credMgr->getCredentialObject($apiUsername );
		$headers = $this->getPayPalHeaders($apIPPCredential, $config, $connection);
		
		
		//TODO: logic for checking if '/'	is set properly	
		$url = $this->endpoint . $this->serviceName . '/' . $apiMethod;
		$params = $this->marshall($params);
		$this->logger->info("Request: $params");
		$response = $connection->execute($url, $params, $headers);
		$this->logger->info("Response: $response");
		return $response;
	}

	private function marshall($object)
	{
		$transformer = new PPObjectTransformer();		
		return $transformer->toString($object);
	}	
	
}
