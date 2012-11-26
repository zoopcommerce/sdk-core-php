<?php
require_once 'PPCredentialManager.php';
require_once 'PPConnectionManager.php';
require_once 'PPHttpConfig.php';
require_once 'PPObjectTransformer.php';
require_once 'PPLoggingManager.php';
require_once 'PPRequest.php';
require_once 'PPUtils.php';
require_once dirname(__FILE__) . '/formatters/PPNVPFormatter.php';
require_once dirname(__FILE__) . '/formatters/PPSOAPFormatter.php';
require_once dirname(__FILE__) . '/handlers/PPAuthenticationHandler.php';

class PPAPIService {
	
	public $endpoint;
	public $serviceName;
	private $logger;
	private $handlers = array();
	private $serviceBinding;

	public function __construct($serviceName, $serviceBinding, $handlers=array()) {
		$this->serviceName = $serviceName;
		$config = PPConfigManager::getInstance();
		$this->endpoint = $config->get('service.EndPoint');
		$this->logger = new PPLoggingManager(__CLASS__);
		$this->handlers = $handlers;
		$this->serviceBinding = $serviceBinding;
	}

	public function setServiceName($serviceName) {
		$this->serviceName = $serviceName;
	}

	public function addHandler($handler) {
		$this->handlers[] = $handler;
	}

	public function makeRequest($apiMethod, $params, $apiUsername = null, $accessToken = null, $tokenSecret = null) {

		$config = PPConfigManager::getInstance();
		if(is_string($apiUsername) || is_null($apiUsername)) {
			// $apiUsername is optional, if null the default account in config file is taken
			$credMgr = PPCredentialManager::getInstance();
			$apiCredential = $credMgr->getCredentialObject($apiUsername );
		} else {
			$apiCredential = $apiUsername; //TODO: Aargh
		}
		if(isset($accessToken) && isset($tokenSecret)) {
			$apiCredential->setThirdPartyAuthorization(
				new PPTokenAuthorization($accessToken, $tokenSecret));
		}
		
		if($this->serviceBinding == 'SOAP' ) {
			$url = $this->endpoint;
			$formatter = new PPSOAPFormatter();
		} else {
			$url = $this->endpoint . $this->serviceName . '/' . $apiMethod;
			$formatter = new PPNVPFormatter();
		}

		$request = new PPRequest($params, $this->serviceBinding);
		$httpConfig = new PPHttpConfig($url);
		$this->runHandlers($httpConfig, $request, $apiCredential);
		
		$payload = $formatter->toString($request);
		$connection = PPConnectionManager::getInstance()->getConnection($httpConfig);
		$this->logger->info("Request: $payload");
		$response = $connection->execute($payload);
		$this->logger->info("Response: $response");
		
		return array('request' => $payload, 'response' => $response);
	}

	private function runHandlers($httpConfig, $request, $apiCredential) {
		$handler = new PPAuthenticationHandler($apiCredential);
		$handler->handle($httpConfig, $request);
		foreach($this->handlers as $handlerClass) {
			$handler = new $handlerClass($apiCredential);
			$handler->handle($httpConfig, $request);
		}
	}

}
