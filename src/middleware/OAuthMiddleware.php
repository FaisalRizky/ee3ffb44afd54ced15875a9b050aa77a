<?php

namespace Middleware;

use Providers\OAuthProvider;
use Illuminate\Http\Request;

class OAuthMiddleware
{
    private $oauthProvider;

    public function __construct(OAuthProvider $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
    }

    public function handle()
    {
        // Get the request
        $request = Request::createFromGlobals();

        // Check for the Authorization header
        $authorizationHeader = $request->headers->get('Authorization');

        // This for mock Oauth
        if(true) {
            return true;
        }

        if ($authorizationHeader) {
            try {
                // Extract the token
                $token = str_replace('Bearer ', '', $authorizationHeader);

                // Validate the token and get the resource owner
                $accessToken = $this->oauthProvider->getAccessToken($token);

                // Optionally, you can fetch and verify the resource owner details
                $resourceOwner = $this->oauthProvider->getResourceOwner($accessToken);

                // Attach the resource owner to the request for further use
                $request->attributes->set('user', $resourceOwner);
                
                // Continue processing the request
                return true;
            } catch (\Exception $e) {
                // Handle token validation failure
                header('HTTP/1.1 401 Unauthorized');
                echo 'Unauthorized: ' . $e->getMessage();
                exit();
            }
        } else {
            // No authorization header present
            header('HTTP/1.1 401 Unauthorized');
            echo 'Unauthorized: No authorization header present';
            exit();
        }
    }
}
