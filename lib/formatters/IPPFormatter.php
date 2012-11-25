<?php
/**
 * Interface for all classes that format objects to
 * and from a on-wire representation
 * 
 * For every new payload format, write a new formatter
 * class that implements this interface
 *
 */
interface IPPFormatter {
	
	public function toString($object, $options=array());
	
	public function toObject($string, $options=array());
}