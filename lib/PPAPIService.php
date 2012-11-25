<?php
require_once 'PPCredentialManager.php';
require_once 'PPConnectionManager.php';
require_once 'PPHttpConfig.php';
require_once 'PPObjectTransformer.php';
require_once 'PPLoggingManager.php';
require_once 'PPUtils.php';
require_once dirname(__FILE__) . '/formatters/PPNVPFormatter.php';
require_once dirname(__FILE__) . '/formatters/PPSOAPFormatter.php';
require_once dirname(__FILE__) . '/handlers/PPAuthenticationHandler.php';

class PPAPIService {
	
	public $endpoint;
	public $serviceName;
	private $logger;
	private $handlers = array();

	public function __construct($serviceName, $handlers=array()) {
		$this->serviceName = $serviceName;
		$config = PPConfigManager::getInstance();
		$this->endpoint = $config->get('service.EndPoint');
		$this->logger = new PPLoggingManager(__CLASS__);
		$this->handlers = $handlers;
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
		
		if($config->get('service.Binding') == 'SOAP' ) {
			$url = $this->endpoint;
			$formatter = new PPSOAPFormatter();
		} else {
			$url = $this->endpoint . $this->serviceName . '/' . $apiMethod;
			$formatter = new PPNVPFormatter();
		}

		$httpConfig = new PPHttpConfig($url);
		$this->runHandlers($httpConfig, $apiCredential);
		
		$payload = $formatter->toString($params);
		$connection = PPConnectionManager::getInstance()->getConnection($httpConfig);
		$this->logger->info("Request: $payload");
		$response = $connection->execute($payload);
		$this->logger->info("Response: $response");
		
		return array('request' => $payload, 'response' => $response);
	}

	private function runHandlers($httpConfig, $apiCredential) {
		foreach($this->handlers as $handlerClass) {
			$handler = new $handlerClass($apiCredential);
			$handler->handle($httpConfig);
		}
		$handler = new PPAuthenticationHandler($apiCredential);
		$handler->handle($httpConfig);		
	}

}
