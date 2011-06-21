<?php
require_once 'PHPUnit/Framework.php';
require_once 'services/Invoice/Invoice.php';

class NVPTransformerTest extends PHPUnit_FrameWork_TestCase {

	/**
	 * @test
	 */				
	public function checkSimpleSerialization() {
		
		$env = new RequestEnvelope();
		$env->errorLanguage = "en_US";		
		$this->assertEquals($env->toNVPString(), "errorLanguage=en_US");
		
		$env->detailLevel = "ReturnAll";
		$this->assertEquals($env->toNVPString(), "detailLevel=ReturnAll&errorLanguage=en_US");
	}	
	
	/**
	 * @donottest
	 */				
	public function checkNestedObjectSerialization() {
		
		$req = new GetVerifiedStatusRequest();		
		$req->emailAddress = "me@gmail.com";
		
		$this->assertEquals($req->toNVPString(), "emailAddress=me%40gmail.com");						
		
		$env = new RequestEnvelope();
		$env->errorLanguage = "en_US";		
		$req->requestEnvelope = $env;
		$this->assertEquals($req->toNVPString(), "requestEnvelope.errorLanguage=en_US&emailAddress=me%40gmail.com");		
		
		$env->detailLevel = "ReturnAll";
		$this->assertEquals($req->toNVPString(), "requestEnvelope.detailLevel=ReturnAll&requestEnvelope.errorLanguage=en_US&emailAddress=me%40gmail.com");
		
		
		
		$req = new CreateAccountRequest();
		
		$env = new RequestEnvelope();
		$env->errorLanguage = "en_US";
		$env->detailLevel = "ReturnAll";
		$req->requestEnvelope = $env;

		$bInfo = new BusinessInfoType();
		$req->businessInfo = $bInfo;
		
		$bInfo->businessName = "VidyaInc";
		$addr = new AddressType();
		$addr->line1 = "line1";
		$addr->line2 = "line2";
		$bInfo->businessAddress = $addr; 
		
		//echo $req->toNVPString();
		$this->assertEquals($req->toNVPString(), "requestEnvelope.detailLevel=ReturnAll&requestEnvelope.errorLanguage=en_US&businessInfo.businessName=VidyaInc&businessInfo.businessAddress.line1=line1&businessInfo.businessAddress.line2=line2");
	}
	
	/**
	 * @test
	 */		
	public function checkCollectionObjectSerialization() {
		
	}	
}
?>