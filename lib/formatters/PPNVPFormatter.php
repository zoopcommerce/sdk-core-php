<?php
require_once 'IPPFormatter.php';

class PPNVPFormatter implements IPPFormatter {
	
	public function toString($object, $options=array()) {		
		return $object->toNVPString();
	}
	
	public function toObject($string, $options=array()) {
		throw new BadMethodCallException("Unimplemented");
	}
}
