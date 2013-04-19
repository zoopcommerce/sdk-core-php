<?php

class Authorization {
	
	/**
	 * Get the PayPal URL to which the user must be redirected to 
	 * start the authentication / authorization process.
	 *  
	 * @param string $redirectUri Uri on merchant website to where
	 * 				the user must be redirected to post paypal login
	 * @param array $scope The access privilges that you are requesting for
	 * 				from the user. Pass empty array for all scopes.
	 * 				See https://developer.paypal.com/webapps/developer/docs/classic/loginwithpaypal/ht_OpenIDConnect/#parameters for more
	 * @param array $config Optional SDK configuration
	 */
	public static function getRedirectUrl($redirectUri, $scope, $config=null) {
		
		if(is_null($config)) {
			$config = PPConfigManager::getInstance()->getConfigHashmap();
		}

		$baseUrl = array_key_exists('openid.RedirectUri', $config) ? $config['openid.RedirectUri'] : 
			PPConstants::OPENID_REDIRECT_LIVE_URL;
		$scope = count($scope) != 0 ? $scope : array('openid', 'profile', 'address', 'email', 'phone', 'https://uri.paypal.com/services/paypalattributes');
		if(!in_array('openid', $scope)) {
			$scope[] = 'openid';
		}		
		$params = array(
			'client_id' => $config['acct1.ClientId'],
			'response_type' => 'code',
			'scope' => implode(" ", $scope),
			'redirect_uri' => $redirectUri
		);
		return sprintf("%s/v1/authorize?%s", $baseUrl, http_build_query($params));		
	}
}