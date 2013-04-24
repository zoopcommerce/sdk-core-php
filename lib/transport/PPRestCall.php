<?php

class PPRestCall {

	
	/**
	 * 
	 * @var PPLoggingManager logger interface
	 */
	private $logger;

	public function __construct() {
		$this->logger = new PPLoggingManager(__CLASS__);
	}

	/**
	 * @param APIContext $apiContext API context for this call
	 * @param array $handlers array of handlers
	 * @param string $path   Resource path relative to base service endpoint
	 * @param string $method HTTP method - one of GET, POST, PUT, DELETE, PATCH etc
	 * @param string $data   Request payload
	 * @param array $headers HTTP headers
	 */
	public function execute($apiContext, $handlers, $path, $method, $data='', $headers=array()) {

		$config = $apiContext->getConfig();		
		$httpConfig = new PPHttpConfig(null, $method);
		$httpConfig->setHeaders($headers + 
			array(
				'Content-Type' => 'application/json'
			)	
		);
		
		foreach($handlers as $handler) {
			$handler = new $handler($apiContext);
			$handler->handle($httpConfig, $data, array('path' => $path));
		}
		$connection = new PPHttpConnection($httpConfig, $config);
		$response = $connection->execute($data);
		$this->logger->fine($response);
		
		return $response;
	}
	
	
}
