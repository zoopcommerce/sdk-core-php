<?php

class PPOpenIdSession {
	
	/**
	 * Returns the PayPal URL to which the user must be redirected to 
	 * start the authentication / authorization process.
	 *  
	 * @param string $redirectUri Uri on merchant website to where
	 * 				the user must be redirected to post paypal login
	 * @param array $scope The access privilges that you are requesting for
	 * 				from the user. Pass empty array for all scopes.
	 * 				See https://developer.paypal.com/webapps/developer/docs/classic/loginwithpaypal/ht_OpenIDConnect/#parameters for more
	 * @param array $config Optional SDK configuration
	 */
	public static function getAuthorizationUrl($redirectUri, $scope, $config=null) {
		
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
	
	
	/**
	 * Returns the URL to which the user must be redirected to
	 * logout from the OpenID provider (i.e. PayPal)
	 *
	 * @param string $redirectUri Uri on merchant website to where
	 * 				the user must be redirected to post logout
	 * @param string $idToken id_token from the TokenInfo object
	 * @param array $config Optional SDK configuration
	 */
	public static function getLogoutUrl($redirectUri, $idToken, $config=null) {
		$baseUrl = array_key_exists('openid.RedirectUri', $config) ? $config['openid.RedirectUri'] :
			PPConstants::OPENID_REDIRECT_LIVE_URL;
		$params = array(
			'id_token' => $idToken,
			'redirect_uri' => $redirectUri,
			'logout' => 'true'
		);
		return sprintf("%s/v1/endsession?%s", $baseUrl, http_build_query($params));
	}
}