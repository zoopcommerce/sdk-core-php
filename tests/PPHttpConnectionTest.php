<?php
require_once 'PHPUnit/Framework.php';

require_once 'PPHttpConnection.php';

/**
 * Test class for PPHttpConnection.
 * 
 */
class PPHttpConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PPHttpConnection
     */
    protected $object;
    public $certPath;
    private $headers_arr =  array();
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PPHttpConnection;
        $this->certPath = 'CertPath';
    }
    private function setPayPalHeaders()
	{
			
		$this->headers_arr[] = "X-PAYPAL-SECURITY-USERID: jb-us-seller1_api1.paypal.com " ;
		$this->headers_arr[] = "X-PAYPAL-SECURITY-PASSWORD: Y382QH72D4MQYJT3";
		$this->headers_arr[] = "X-PAYPAL-SECURITY-SIGNATURE: AO5YpL3NU.zkhUje4dIKD33KcJ9wARbnOle0-IpAhVGFqDVlwmmQ.vSV";
		// Add other headers
		$this->headers_arr[] = "X-PAYPAL-APPLICATION-ID: APP-5XV204960S3290106";
		$this->headers_arr[] = "X-PAYPAL-REQUEST-DATA-FORMAT: NV";
		$this->headers_arr[] = "X-PAYPAL-RESPONSE-DATA-FORMAT: NV";

		
	}

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @test
     */
    public function testExecute()
    {
    	$this->setPayPalHeaders();
    	 $this->object->setHttpHeaders($this->headers_arr);
    	  $this->object->setHttpTrustAllConnection(true);
    	  $this->object->setHttpTimeout(60);
    	  $this->object->setHttpRetry(4);
    	$res = $this->object->execute('https://stage2sc5376.sc4.paypal.com:10630/Invoice/CreateInvoice', 'Invalid Params');
    	
    	$this->setExpectedException('PPConnectionException');
    	$res = $this->object->execute('Invalid', 'Invalid Params');
    	$res = $this->object->execute('https://stage2sc5376.sc4.paypal.com:10630//CreateInvoice', 'Invalid Params');
    	
    }
/**
     * @test
     */
    public function testSetHttpProxy()
    {
    	$this->setExpectedException('PPConfigurationException');
    	$this->object->setHttpProxy('InvalidProxy');
    	
    }
    
}
?>
