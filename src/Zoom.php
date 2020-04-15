<?php namespace Daycry\Zoom;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Daycry\Zoom\Exceptions\ZoomException;

class Zoom
{
	private $apiUrl = 'https://api.zoom.us/v2/';

    private $provider = null;

    protected $clientId = null;

    protected $clientSecret = null;

	protected $accessToken = null;

    protected $redirectUrl = null;

	protected $request = null;

    public function __construct( BaseConfig $config = null )
    {
        if( empty( $config ) )
        {
            $config = config( 'Zoom' );
        }

        if( isset( $config->clientId ) )
        {
            $this->clientId = $config->clientId;
        }
        
        if( isset( $config->clientSecret ) )
        {
            $this->clientSecret = $config->clientSecret;
        }

        if( isset( $config->redirectUrl ) )
        {
            $this->redirectUrl = $config->redirectUrl;
        }

        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider(
			[
				'clientId'                => $this->clientId,       // The client ID assigned to you by the provider
				'clientSecret'            => $this->clientSecret,   // The client password assigned to you by the provider
				'redirectUri'             => $this->redirectUrl,    // Url to redirect response
				'urlAuthorize'            => 'https://zoom.us/oauth/authorize',
				'urlAccessToken'          => 'https://zoom.us/oauth/token',
				'urlResourceOwnerDetails' => 'https://api.zoom.us/v2/users/me'
			]
		);

		$this->request = \Config\Services::request();
    }

	public function authentication()
	{	
		$session = \Config\Services::session();

		// If we don't have an authorization code then get one
        if ( !$this->request->getGet( 'code' ) )
        {
			$authorizationUrl = $this->provider->getAuthorizationUrl();

			// Get the state generated for you and store it to the session.
			$session->set( 'oauth2state', $this->provider->getState() );
			//$_SESSION['oauth2state'] = $provider->getState();
		
			// Redirect the user to the authorization URL.
			header('Location: ' . $authorizationUrl);
			exit;
		
		// Check given state against previously stored one to mitigate CSRF attack
		} elseif ( empty( $this->request->getGet( 'state' ) ) || ( $session->has( 'oauth2state' ) && $this->request->getGet( 'state' ) !== $session->get( 'oauth2state' ) ) )
		{
		
			if( $session->has( 'oauth2state' ) )
			{
				$session->remove( 'oauth2state' );
			}
			
			throw ZoomException::forIncorrectState();
		
		} else {

			// Try to get an access token using the authorization code grant.
			$this->accessToken = $this->provider->getAccessToken('authorization_code', [
				'code' => $this->request->getGet( 'code' )
			]);
	
			return $this->accessToken->jsonSerialize();

			// We have an access token, which we may use in authenticated
			// requests against the service provider's API.
			//echo 'Access Token: ' . $this->accessToken->getToken() . "<br>";
			//echo 'Refresh Token: ' . $this->accessToken->getRefreshToken() . "<br>";
			//echo 'Expired in: ' . $this->accessToken->getExpires() . "<br>";
			//echo 'Already expired? ' . ( $this->accessToken->hasExpired() ? 'expired' : 'not expired' ) . "<br>";
	
			// Using the access token, we may look up details about the
			// resource owner.
			//$resourceOwner = $this->provider->getResourceOwner( $this->accessToken );
	
			
		}
	}

	public function refreshAccessToken( array $token = [] )
	{
		$newAccessToken = [];

		if( !empty( $token ) )
		{
			$this->accessToken = new AccessToken( $token );
		}

		if( $this->accessToken->hasExpired() )
		{
			$newAccessToken = $this->provider->getAccessToken('refresh_token', [
				'refresh_token' => $this->accessToken->getRefreshToken()
			])->jsonSerialize();
		}

		return $newAccessToken;
	}

	public function getResourceOwner()
	{
		return $this->provider->getResourceOwner( $this->accessToken );
	}

	public function setAccessToken( array $token = [] )
    {
        if( !empty( $token ) )
		{
			$this->accessToken = new AccessToken( $token );
		}
	}
	
	public function request( string $type = 'GET', string $url = '', array $options = [], array $token = [] )
	{
		if( !empty( $token ) )
		{
			$this->setAccessToken( $token );
		}

		if( $this->accessToken->hasExpired() )
		{
			throw ZoomException::forTokenExpired();
		}

		$response = $this->provider->getAuthenticatedRequest(
			$type,
			$this->apiUrl . $url,
			$this->accessToken,
			$options
		);

		return $this->provider->getParsedResponse( $response );
	}
	//--------------------------------------------------------------------
}
