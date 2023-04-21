<?php

namespace Splash\Bundle\Security;

use Splash\Bundle\Interfaces\AuthenticatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Splash Bundle Authenticator, Only Used on Toolkit for Testing Connectors
 */
class ConnectorAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var AuthenticatorInterface[]
     */
    private array $authenticators = array();

    /**
     * Register a Tagged Standalone Object Service
     *
     * @param AuthenticatorInterface $authenticator
     *
     * @return void
     */
    public function registerAuthenticator(AuthenticatorInterface $authenticator): void
    {
        $this->authenticators[get_class($authenticator)] = $authenticator;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): bool
    {
        //==============================================================================
        // Authenticator Only Works on this URI
        if ($request->attributes->get('_route') !== 'splash_connector_oauth2_connect') {
            return false;
        }
        //==============================================================================
        // Walk on Registered Authenticators
        foreach ($this->authenticators as $authenticator) {
            if ($authenticator->supports($request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials(Request $request): array
    {
        //==============================================================================
        // Walk on Registered Authenticators
        foreach ($this->authenticators as $authenticator) {
            $credentials = $authenticator->getCredentials($request);
            if (!empty($credentials)) {
                return $credentials;
            }
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        //==============================================================================
        // This is a Tests Authenticator, Loading Default Toolkit User
        return $userProvider->loadUserByUsername("toolkit@splashsync.com");
    }

    /**
     * {@inheritDoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        //==============================================================================
        // Ensure Credentials found
        if (!is_array($credentials) || empty($credentials)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        // on success, let the request continue
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}