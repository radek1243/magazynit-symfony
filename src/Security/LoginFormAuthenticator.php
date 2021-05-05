<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;
    
    public const LOGIN_ROUTE = 'index';
    
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /*if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }*/
        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));       
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    public function onAuthenticationFailure(Request $request, $exception){
        $request->getSession()->remove(Security::LAST_USERNAME);
        $request->getSession()->set(SECURITY::AUTHENTICATION_ERROR, "Błędny login lub hasło");
        return new RedirectResponse($this->urlGenerator->generate('index'));
    }
    
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        //dd($credentials); jest ok
        /*$token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }*/
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $credentials['login']]);
        if ($user==null) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('User could not be found.');
        }        
        return $user;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
        && $request->isMethod('POST') && !$request->isXmlHttpRequest();
    }

    public function getCredentials(Request $request)
    {
        //dd($request->request->all());     //jest ok
        $credentials = $request->request->all('form');
        /*$credentials = array('login' => $request->request->get('login'),
                            'pass' => $request->request->get('pass'),
                            'csrf_token' => $request->request->get('_csrf_token')
        );*/
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['login']
            );
        return $credentials;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        //dd($user);  jest ok
        //dd($credentials, $user->getPassword(), $this->passwordEncoder->encodePassword($user, $credentials['pass']));
        return $this->passwordEncoder->isPasswordValid($user, $credentials['pass']);
    }
    public function getPassword($credentials): ?string
    {
        return $credentials['pass'];
    }

}

