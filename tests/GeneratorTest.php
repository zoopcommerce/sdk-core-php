<?php
require_once 'PHPUnit/Framework.php';
require_once("services/Invoice/InvoiceService.php");


class InvoiceGeneratorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */				
	public function checkAPIWrapperClass() {
		
		$className = "InvoiceService";
		$this->assertClassHasAttribute("serviceName", $className);

		$aa = new InvoiceService();	
		$this->assertEquals($aa->getServiceName(), "Invoice");
	}

	/**
	 * @test
	 */				
	public function checkCreateAndSendInvoiceStubs() {
		
		$className = "CreateAndSendInvoiceRequest";		
		$createAccountReq = new $className();		
		$attribs = array('requestEnvelope', 'invoice');
		foreach($attribs as $attrib) {
			$this->assertClassHasAttribute($attrib, $className);	
		}

		$className = "CreateAndSendInvoiceResponse";		
		$createAccountReq = new $className();		
		$attribs = array('responseEnvelope', 'invoiceID', 
						'invoiceNumber', 'error');
		foreach($attribs as $attrib) {
			$this->assertClassHasAttribute($attrib, $className);	
		}		 		
		
		$className = "InvoiceItemType";		
		$createAccountReq = new $className();		
		$attribs = array('name', 'description', 'date', 'quantity', 'unitPrice',
						'taxName', 'taxRate');
		foreach($attribs as $attrib) {
			$this->assertClassHasAttribute($attrib, $className);	
		}		
	}
	
	/**
	 * @test
	 * Test methods on the service stub
	 */
	public function checkOperationWrapperFunction() {
		$className = "InvoiceService";
		
		$reflector = new ReflectionClass($className);
		$m = $reflector->getMethod("CreateAndSendInvoice");
		$this->assertNotNull($m);		
	}
	
	/**
	 * @test
	 */				
	public function checkWSDLAnnotationIsParsed() {
		
	}
	
}
?>