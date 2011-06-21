<?php
require_once 'PPAPIService.php';


class PPBaseService {
	
	private $serviceName;
	
	public function __construct($serviceName) {
		$this->serviceName = $serviceName;
	}
	
	public function getServiceName() {
		return $this->serviceName;
	}
	
	public function call($method, $requestObject, $apiUsername = null) {
		$service = new PPAPIService();
		$service->setServiceName($this->serviceName);
		return $service->makeRequest($method, $requestObject, $apiUsername);
	}
}
?>
