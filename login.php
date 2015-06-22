<?php
	session_start();
	
	if( isset( $_SESSION[ 'SteamID' ] ) )
	{
		header( 'Location: /' );
		die;
	}
	
	if( isset( $_GET[ 'openid_identity' ] ) )
	{
		$ErrorMessage = false;
		$CommunityID = SteamOpenID::ValidateLogin( $ErrorMessage, SteamOpenID::GetHost() );
		
		if( $CommunityID === false )
		{
			echo $ErrorMessage === false ? 'Something went horribly wrong, please try again later.' : 'OpenID error: <b>' . htmlentities( $ErrorMessage ) . '</b>';
			
			die;
		}
		
		$_SESSION[ 'SteamID' ] = $CommunityID;
		//$_SESSION[ 'Avatar' ] = $Info[ 'Avatar' ];
		
		header( 'Location: /' );
	}
	else
	{
		header( 'Location: ' . SteamOpenID::GenerateLoginURL( '/login.php' ) );
	}
	
	class SteamOpenID
	{
		const STEAM_LOGIN = 'https://steamcommunity.com/openid/login';
		
		public static function GetHost()
		{
			return ( ( Empty( $_SERVER[ 'HTTPS' ] ) || $_SERVER[ 'HTTPS' ] === 'off' ) ? 'http' : 'https' ) . '://' . $_SERVER[ 'HTTP_HOST' ];
		}
		
		public static function GenerateLoginURL( $ReturnTo )
		{
			$Host = self::GetHost();
			
			$Parameters = Array(
				'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
				'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
				'openid.ns'         => 'http://specs.openid.net/auth/2.0',
				'openid.mode'       => 'checkid_setup',
				'openid.realm'      => $Host,
				'openid.return_to'  => $Host . $ReturnTo
			);
			
			return self::STEAM_LOGIN . '?' . HTTP_Build_Query( $Parameters, '', '&' );
		}
		
		/*
		 * Validates OpenID data, and verifies with Steam
		 *
		 * $ErrorMessage by-ref string, if this function returns false, this parameter will contain an error message
		 *
		 * @return string Returns the CommunityID if successful or false on failure
		 */
		public static function ValidateLogin( &$ErrorMessage, $SelfURL )
		{
			$Mode = Filter_Input( INPUT_GET, 'openid_mode', FILTER_SANITIZE_SPECIAL_CHARS );
			
			if( $Mode === 'error' )
			{
				$ErrorMessage = Filter_Input( INPUT_GET, 'openid_error', FILTER_SANITIZE_STRING );
				
				if( Empty( $ErrorMessage ) )
				{
					$ErrorMessage = 'Something went wrong.';
				}
				
				return false;
			}
			else if( $Mode !== 'id_res' )
			{
				$ErrorMessage = 'Invalid OpenID mode.';
				
				return false;
			}
			
			// See http://openid.net/specs/openid-authentication-2_0.html#positive_assertions
			$Arguments = Filter_Input_Array( INPUT_GET, Array(
				'openid_ns' => Array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => Array( 'regexp' => '/^http:\/\/specs\.openid\.net\/auth\/2\.0$/' )
				),
				'openid_op_endpoint' => Array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => Array( 'regexp' => '/^' . Preg_Quote( self::STEAM_LOGIN, '/' ) . '$/' )
				),
				'openid_claimed_id' => Array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => Array( 'regexp' => '/^https?:\/\/steamcommunity.com\/openid\/id\/(7656119[0-9]{10})\/?$/' )
				),
				'openid_identity' => FILTER_SANITIZE_URL,
				'openid_return_to' => FILTER_SANITIZE_URL, // Should equal to url we sent
				'openid_response_nonce' => FILTER_SANITIZE_STRING,
				'openid_assoc_handle' => FILTER_SANITIZE_SPECIAL_CHARS, // Steam just sends 1234567890
				'openid_signed' => FILTER_SANITIZE_SPECIAL_CHARS,
				'openid_sig' => FILTER_SANITIZE_SPECIAL_CHARS
			) );
			
			if( !Is_Array( $Arguments ) )
			{
				$ErrorMessage = 'Invalid arguments.';
				
				return false;
			}
			else if( In_Array( null || false, $Arguments ) ) // Yeah, input filter is that stupid
			{
				$ErrorMessage = 'One of the arguments is invalid and/or missing.';
				
				return false;
			}
			else if( $Arguments[ 'openid_claimed_id' ] !== $Arguments[ 'openid_identity' ] )
			{
				$ErrorMessage = 'Claimed id must match your identity.';
				
				return false;
			}
			else if( strpos( $Arguments[ 'openid_return_to' ], $SelfURL ) !== 0 )
			{
				$ErrorMessage = 'Invalid return uri.';
				
				return false;
			}
			
			if( Preg_Match( '/^https?:\/\/steamcommunity.com\/openid\/id\/(7656119[0-9]{10})\/?$/', $Arguments[ 'openid_identity' ], $CommunityID ) === 1 )
			{
				$CommunityID = $CommunityID[ 1 ];
			}
			// In theory, this should never happen
			else
			{
				$ErrorMessage = 'Failed to find your CommunityID. If this issue persists, please contact us.';
				
				return false;
			}
			
			$Arguments[ 'openid_mode' ] = 'check_authentication'; // Add mode for verification
			
			$c = cURL_Init( );
			
			cURL_SetOpt_Array( $c, Array(
				CURLOPT_USERAGENT      => 'Steam Database Party OpenID Login',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL            => self::STEAM_LOGIN,
				CURLOPT_CONNECTTIMEOUT => 6,
				CURLOPT_TIMEOUT        => 6,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $Arguments // According to specs, openid.signed can contain other keys for signature validation, but we don't care, right?
			) );
			
			$Response = cURL_Exec( $c );
			
			cURL_Close( $c );
			
			if( Preg_Match( '/is_valid\s*:\s*true/', $Response ) === 1 )
			{
				return $CommunityID;
			}
			
			// If we reach here, then it failed
			$ErrorMessage = 'Failed to verify your login with Steam, it could be down. Check Steam\'s status at http://steamstat.us.';
			
			return false;
		}
	}
