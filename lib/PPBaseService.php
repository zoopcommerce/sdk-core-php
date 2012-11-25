<?php
require_once 'PPAPIService.php';


class PPBaseService {

	private $serviceName;
	private $handlers;

   /*
    * Setters and getters for Third party authentication (Permission Services)
    */
	protected $accessToken;
	protected $tokenSecret;
	protected $lastRequest;
	protected $lastResponse;

    public function getLastRequest() {
		return $this->lastRequest;
	}
    public function setLastRequest($lastRqst) {
		$this->lastRequest = $lastRqst;
	}
    public function getLastResponse() {
		return $this->lastResponse;
	}
    public function setLastResponse($lastRspns) {
		$this->lastResponse = $lastRspns;
	}

	public function getAccessToken() {
		return $this->accessToken;
	}
	public function setAccessToken($accessToken) {
		$this->accessToken = $accessToken;
	}
	public function getTokenSecret() {
		return $this->tokenSecret;
	}
	public function setTokenSecret($tokenSecret) {
		$this->tokenSecret = $tokenSecret;
	}

	public function __construct($serviceName, $handlers=array()) {
		$this->serviceName = $serviceName;
		$this->handlers = $handlers;
	}

	public function getServiceName() {
		return $this->serviceName;
	}

	public function call($method, $requestObject, $apiUsername = null) {		
		$service = new PPAPIService($this->serviceName, $this->handlers);		
		$ret = $service->makeRequest($method, $requestObject, $apiUsername ,$this->accessToken, $this->tokenSecret);
		$this->lastRequest = $ret['request'];
		$this->lastResponse = $ret['response'];
		return $this->lastResponse;
	}

    private function marshall($object) {
		$transformer = new PPObjectTransformer();
		return $transformer->toString($object);
	}
}
?>
