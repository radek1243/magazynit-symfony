<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class LoginPassAuthenticator extends AbstractLoginFormAuthenticator{

    public const LOGIN_ROUTE = 'index';

    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
        && $request->isMethod('POST') && !$request->isXmlHttpRequest();
    }

    public function authenticate(Request $request): PassportInterface
    {
        $passport = new Passport(new UserBadge($request->request->get('form')['login']), new PasswordCredentials($request->request->get('form')['pass']));
        $request->getSession()->set(Security::LAST_USERNAME, $request->request->get('form')['login']);
        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $request->getSession()->remove(Security::LAST_USERNAME);
        $request->getSession()->set(SECURITY::AUTHENTICATION_ERROR, "Błędny login lub hasło");
        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate($this::LOGIN_ROUTE);
    }

}