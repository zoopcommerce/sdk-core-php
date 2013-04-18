<?php

class Address extends PPModel {

		
		/**
		 * Full street address component, which may include house number, street name.
		 * @return string
		 */
		 public function getStreetAddress() {
		 	return $this->street_address;
		 }
		
		/**
		 * City or locality component.
		 * @return string
		 */
		 public function getLocality() {
		 	return $this->locality;
		 }
		
		/**
		 * State, province, prefecture or region component.
		 * @return string
		 */
		 public function getRegion() {
		 	return $this->region;
		 }
		
		/**
		 * Zip code or postal code component.
		 * @return string
		 */
		 public function getPostalCode() {
		 	return $this->postal_code;
		 }
		
		/**
		 * Country name component.
		 * @return string
		 */
		 public function getCountry() {
		 	return $this->country;
		 }

}
