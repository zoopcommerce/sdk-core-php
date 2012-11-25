<?php

require_once 'IPPFormatter.php';

class PPSOAPFormatter implements IPPFormatter {
	
	public function toString($object, $options=array()) {
		
		$soapEnvelope = '<soapenv:Envelope ' . implode(' ', $options['namespace']) . '>';

		$soapHeader = '<soapenv:Header>';
		$soapHeader = '</soapenv:Header>';
				
		$soapBody = '<soapenv:Body>';
		$soapBody .= $object->toXMLString();
		$soapBody .= '</soapenv:Body>';
		
		return $soapEnvelope . $soapHeader . $soapBody . '</soapenv:Envelope>';
	}
	
	public function toObject($string, $options=array()) {
		throw new BadMethodCallException("Unimplemented");
	}	
}
