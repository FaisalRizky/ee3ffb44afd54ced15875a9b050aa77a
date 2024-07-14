<?php

namespace Src\Middleware;

use Providers\OAuthProvider;

class OAuthMiddleware
{
    private $oauthProvider;

    public function __construct(OAuthProvider $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
    }

    public function handle()
    {
        try {
            $accessToken = $this->oauthProvider->getAccessTokenFromRequest();
            $resourceOwner = $this->oauthProvider->getResourceOwner($accessToken);

            // You can perform additional checks or set user information here
            // e.g., $_SESSION['user'] = $resourceOwner;

        } catch (\Exception $e) {
            header('HTTP/1.1 401 Unauthorized');
            echo 'Unauthorized: ' . $e->getMessage();
            exit();
        }
    }
}
