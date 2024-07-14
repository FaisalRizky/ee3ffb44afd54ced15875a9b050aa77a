<?php

namespace Providers;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Tool\BearerToken;

class OAuthProvider
{
    private $provider;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        // Initialize OAuth2 Provider
        $this->provider = new GenericProvider([
            'clientId'                => $config['OAUTH_CLIENT_ID'],
            'clientSecret'            => $config['OAUTH_CLIENT_SECRET'],
            'redirectUri'             => $config['OAUTH_REDIRECT_URI'],
            'urlAuthorize'            => $config['OAUTH_AUTHORIZE_URL'],
            'urlAccessToken'          => $config['OAUTH_ACCESS_TOKEN_URL'],
            'urlResourceOwnerDetails' => $config['OAUTH_RESOURCE_OWNER_DETAILS_URL'],
        ]);
    }

    // Get authorization URL
    public function getAuthorizationUrl()
    {
        return $this->provider->getAuthorizationUrl();
    }

    // Get access token using authorization code
    public function getAccessToken($authorizationCode)
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $authorizationCode
        ]);
    }

    // Get resource owner details
    public function getResourceOwner($accessToken)
    {
        return $this->provider->getResourceOwner($accessToken);
    }

    // Get access token from request headers
    public function getAccessTokenFromRequest()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return new BearerToken($_SERVER['HTTP_AUTHORIZATION']);
        }

        throw new \Exception('No authorization token provided');
    }
}
