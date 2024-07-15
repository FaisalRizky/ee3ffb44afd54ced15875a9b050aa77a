<?php

namespace Controllers;

use Config\Config;
use Providers\OAuthProvider;
use Illuminate\Http\Request;

class OAuthController
{
    private $oauthProvider;

    public function __construct()
    {
        // Initialize OAuth provider
        $config = new Config();
        $appConfig = $config->getConfig();
        $this->oauthProvider = new OAuthProvider($appConfig);
    }

    public function authorize(Request $request)
    {
        // Generate authorization URL and redirect user
        $authorizationUrl = $this->oauthProvider->getAuthorizationUrl();
        header('Location: ' . $authorizationUrl);
        exit();
    }

    public function handleCallback(Request $request)
    {
        // Check if we have an authorization code
        $authorizationCode = $request->query('code');
        if (!$authorizationCode) {
            return $this->sendError('Authorization code not provided');
        }

        try {
            // Exchange the authorization code for an access token
            $accessToken = $this->oauthProvider->getAccessToken($authorizationCode);

            // Optionally, you can get resource owner details
            $resourceOwner = $this->oauthProvider->getResourceOwner($accessToken);

            // Store the access token and resource owner details as needed
            session_start();
            $_SESSION['access_token'] = $accessToken->getToken();
            $_SESSION['resource_owner'] = $resourceOwner;

            // Redirect to a protected page or home page
            header('Location: /');
            exit();
        } catch (\Exception $e) {
            // Handle errors (e.g., invalid authorization code, network issues)
            return $this->sendError('Error: ' . $e->getMessage());
        }
    }

    public function token(Request $request)
    {
        // This endpoint could handle token exchange if needed (e.g., refresh tokens)
        return 'Token exchange endpoint (mock implementation)';
    }

    public function resourceOwnerDetails(Request $request)
    {
        // Handle fetching resource owner details
        // This is a mock implementation
        return 'Resource owner details endpoint (mock implementation)';
    }

    private function sendError($message)
    {
        // Simple error response (you can customize this)
        header('Content-Type: text/plain');
        echo $message;
        exit();
    }
}
