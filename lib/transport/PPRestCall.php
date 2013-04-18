<?php

class PPRestCall {

	private $config;
	
	private $logger;

	public function __construct($config) {
		$this->config = $config;
		$this->logger = new PPLoggingManager(__CLASS__);
	}

	/**
	 *
	 * @param string $path
	 * @param string $data
	 * @param array $headers
	 */
	public function execute($path, $method, $data='', $headers=array()) {

		$httpConfig = new PPHttpConfig(null, PPHttpConfig::HTTP_POST);
		$httpConfig->setHeaders($headers + 
			array(
				'Content-Type' => 'application/json'
			)	
		);
		
		$handler = new PPRestHandler($this->config);
		$handler->handle($httpConfig, $data, array('path' => $path));

		$connection = new PPHttpConnection($httpConfig, $this->config);
		$response = $connection->execute($data);
		$this->logger->fine($response);

		return $response;
	}
}
