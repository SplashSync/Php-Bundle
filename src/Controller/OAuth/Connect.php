<?php

namespace Splash\Bundle\Controller\OAuth;

use Splash\Connectors\Shopify\OAuth2\ShopifyAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

/**
 * TEST ONLY - Try Authentification of a User from Connector
 */
class Connect extends AbstractController
{
    /**
     * Check if User is Logged In
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        //==============================================================================
        // Verify User is Logged In
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        return new RedirectResponse("/");
    }
}