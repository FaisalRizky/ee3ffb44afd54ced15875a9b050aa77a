<?php

namespace Middleware;

use Providers\BaseMiddlewareProvider;
use Providers\OAuthProvider;

class OAuthMiddleware extends BaseMiddlewareProvider
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
            $this->sendUnauthorizedResponse('Unauthorized: ' . $e->getMessage());
        }
    }
}
