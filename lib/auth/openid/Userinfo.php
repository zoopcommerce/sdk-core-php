<?php

class Userinfo extends PPModel {

		
		/**
		 * Subject - Identifier for the End-User at the Issuer.
		 * @return string
		 */
		 public function getUserId() {
		 	return $this->user_id;
		 }
		
		/**
		 * Subject - Identifier for the End-User at the Issuer.
		 * @return string
		 */
		 public function getSub() {
		 	return $this->sub;
		 }
		
		/**
		 * End-User's full name in displayable form including all name parts, possibly including titles and suffixes, ordered according to the End-User's locale and preferences.
		 * @return string
		 */
		 public function getName() {
		 	return $this->name;
		 }
		
		/**
		 * Given name(s) or first name(s) of the End-User
		 * @return string
		 */
		 public function getGivenName() {
		 	return $this->given_name;
		 }
		
		/**
		 * Surname(s) or last name(s) of the End-User.
		 * @return string
		 */
		 public function getFamilyName() {
		 	return $this->family_name;
		 }
		
		/**
		 * Middle name(s) of the End-User.
		 * @return string
		 */
		 public function getMiddleName() {
		 	return $this->middle_name;
		 }
		
		/**
		 * URL of the End-User's profile picture.
		 * @return string
		 */
		 public function getPicture() {
		 	return $this->picture;
		 }
		
		/**
		 * End-User's preferred e-mail address.
		 * @return string
		 */
		 public function getEmail() {
		 	return $this->email;
		 }
		
		/**
		 * True if the End-User's e-mail address has been verified; otherwise false.
		 * @return boolean
		 */
		 public function getEmailVerified() {
		 	return $this->email_verified;
		 }
		
		/**
		 * End-User's gender.
		 * @return string
		 */
		 public function getGender() {
		 	return $this->gender;
		 }
		
		/**
		 * End-User's birthday, represented as an YYYY-MM-DD format. They year MAY be 0000, indicating it is omited. To represent only the year, YYYY format would be used.
		 * @return string
		 */
		 public function getBirthdate() {
		 	return $this->birthdate;
		 }
		
		/**
		 * Time zone database representing the End-User's time zone
		 * @return string
		 */
		 public function getZoneinfo() {
		 	return $this->zoneinfo;
		 }
		
		/**
		 * End-User's locale.
		 * @return string
		 */
		 public function getLocale() {
		 	return $this->locale;
		 }
		
		/**
		 * End-User's preferred telephone number.
		 * @return string
		 */
		 public function getPhoneNumber() {
		 	return $this->phone_number;
		 }
		
		/**
		 * End-User's preferred address.
		 * @return Address
		 */
		 public function getAddress() {
		 	return $this->address;
		 }
		 
		 /**
		  * End-User's preferred address.
		  * @param Address $address
		  */
		 public function setAddress($address) {
		 	$this->address = $address;
		 }
		
		/**
		 * Verified account status.
		 * @return boolean
		 */
		 public function getVerifiedAccount() {
		 	return $this->verified_account;
		 }
		
		/**
		 * Account type.
		 * @return string
		 */
		 public function getAccountType() {
		 	return $this->account_type;
		 }
		
		/**
		 * Account holder age range.
		 * @return string
		 */
		 public function getAgeRange() {
		 	return $this->age_range;
		 }
		
		/**
		 * Account payer identifier.
		 * @return string
		 */
		 public function getPayerId() {
		 	return $this->payer_id;
		 }

        /**
		 * returns user details
		 *
		 * @path /v1/identity/openidconnect/userinfo
		 * @method GET
		 * @param array $params (allowed values are schema and access_token)
		 * 					schema - (Optional) the schema that is used to return as per openidconnect protocol
		 * 					access_token - 
		 * @param array $config Optional SDK configuration.
		 * @return Userinfo
		 */
		public static function getuserinfo($params, $config=null) {
			static $allowedParams = array( 'schema' => 1);
			
			if(is_null($config)) {
				$config = PPConfigManager::getInstance()->getConfigHashmap();
			}
			if(!array_key_exists('schema', $params)) {
				$params['schema'] = 'openid';
			}
						
			$call = new PPRestCall($config);
			$ret = new Userinfo();
			$ret->fromJson(
				$call->execute("/v1/identity/openidconnect/userinfo?"
					. http_build_query(array_intersect_key($params, $allowedParams)), "GET", "", 
					array(
						'Authorization' => "Bearer " . $params['access_token'],
						'Content-Type'=> 'x-www-form-urlencoded'
					)
				)
			);
			return $ret;
		}

}
