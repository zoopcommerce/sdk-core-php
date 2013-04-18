# PayPal Core SDK - V1.2.0

## Prerequisites

 * PHP 5.2 and above
 * curl extension with support for OpenSSL
 * PHPUnit 3.5 for running test suite (Optional)
 * Composer (Optional - for running test cases)

## Configuration
  
 
## Openid

   * Redirect your buyer to `Authorization::getRedirectUrl($redirectUri, array());` to obtain authorization.
   * Capture the authorization code that is available as a query parameter (`code`) in the redirect url
   * Exchange the authorization code for a access token, refresh token, id token combo
```php
    $token = Tokeninfo::createFromAuthorizationCode(
		array(
			'code' => $authCode
		)
	);
```
   * The access token is valid for a predefined duration and can be used for seamless XO or for retrieving user information
```php
   $user = Userinfo::getuserinfo(
		array(
			'access_token' => $token->getAccessToken()
		)	
	);
```
   * If the access token has expired, you can obtain a new access token using the refresh token from the 3'rd step.
```php
   $token->createFromRefreshToken(array());
``` 
