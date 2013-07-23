<?php


class SimpleXMLTestClass extends PPXmlMessage {
	/**
	 *
	 * @access public
	 * @namespace ebl
	 * @var string
	 */
	public $field1;
	
	/**
	 *
	 * @access public
	 * @namespace ebl
	 * @var string
	 */
	public $field2;
}

class SimpleContainerXMLTestClass extends PPXmlMessage {
	
	/**
	 * @access public
	 * @var string
	 */
	public $field1;
	
	/**
	 * @array
	 * @access public
	 * @var string
	 */
	public $list1;
	
	/**
	 * @array
	 * @access public
	 * @var SimpleXMLTestClass
	 */
	public $list2;
	
	/**
	 * @array
	 * @access public
	 * @var AttributeXMLTestClass
	 */
	public $list3;
	
	/**
	 * @access public
	 * @var SimpleXMLTestClass
	 */
	public $nestedField;
}

class AttributeXMLTestClass extends PPXmlMessage {
	
	/**
	 *
	 * @access public
	 * @attribute
	 * @var string
	 */
	public $attrib1;

	/**
	 *
	 * @access public
	 * @attribute
	 * @var string
	 */
	public $attrib2;
	
	/**
	 *
	 * @access public
	 * @value
	 * @var string
	 */
	public $value;
	
}

/**
 * @hasAttribute
 *
 */
class AttributeContainerXMLTestClass extends PPXmlMessage {
	/**
	 *
	 * @access public
	 * @var AttributeXMLTestClass
	 */
	public $member;
	
		/**
	 * 
     * @array
	 * @access public
	 * @var AttributeXMLTestClass
	 */ 
	public $arrayMember;

}


/**
 * Test class for PPXmlMessage.
 *
 */
class PPXmlMessageTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		
	}
	
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}
	
	
	private function wrapInSoapMessage($str) {
		$str = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:cc="urn:ebay:apis:CoreComponentTypes" xmlns:wsu="http://schemas.xmlsoap.org/ws/2002/07/utility" xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext" xmlns:ed="urn:ebay:apis:EnhancedDataTypes" xmlns:ebl="urn:ebay:apis:eBLBaseComponents" xmlns:ns="urn:ebay:api:PayPalAPI">'
			. '<SOAP-ENV:Body id="_0">'
    		. $str
    		. '</SOAP-ENV:Body></SOAP-ENV:Envelope>';
		return PPUtils::xmlToArray($str);
	}

	/**
	 * @test
	 */
	public function simpleSerialization() {
		
		$o = new SimpleXMLTestClass();
		$o->field1 = "fieldvalue1";
		$o->field2 = "fieldvalue2";

		$this->assertEquals("<ebl:field1>fieldvalue1</ebl:field1><ebl:field2>fieldvalue2</ebl:field2>", $o->toXMLString(''));
		
		$child = new SimpleXMLTestClass();
		$child->field1 = "fieldvalue1";
		$child->field2 = "fieldvalue2";
		
		$parent = new SimpleContainerXMLTestClass();
		$parent->field1 = 'parent';
		$parent->nestedField = $child;
		
		
		$this->assertEquals("<ebl:field1>parent</ebl:field1><ebl:nestedField><ebl:field1>fieldvalue1</ebl:field1><ebl:field2>fieldvalue2</ebl:field2></ebl:nestedField>", $parent->toXMLString(''));
	}
	
	
	/**
	 * @t1est
	 */
	public function nestedSerialization() {
	
		$o = new SimpleXMLTestClass();
		$o->field1 = "fieldvalue1";
		$o->field2 = "fieldvalue2";
	
		$c = new SimpleContainerXMLTestClass();
		$c->nestedField = $o;
		$c->field1 = "abc";
	
		$this->assertEquals("field1=abc&nestedField.field1=fieldvalue1&nestedField.field2=fieldvalue2", $c->toXMLString(''));
	}
	
}