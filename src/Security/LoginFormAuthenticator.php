<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator
 * @package App\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;
    /** @var UrlGeneratorInterface $urlGenerator */
    private $urlGenerator;
    /** @var CsrfTokenManagerInterface $csrfTokenManager */
    private $csrfTokenManager;
    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;


    /**
     * LoginFormAuthenticator constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UrlGeneratorInterface        $urlGenerator
     * @param CsrfTokenManagerInterface    $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        EntityManagerInterface       $entityManager,
        UrlGeneratorInterface        $urlGenerator,
        CsrfTokenManagerInterface    $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager    = $entityManager;
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder  = $passwordEncoder;
    }


    /**
     * Will be called on the start of every Request.
     * If `false` request continues anonymously to the controller.
     *      It's not an authentication failure - it's just that nothing happens at all.
     * If `true` the `getCredentials()` will be invoked
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }


    /**
     * Read credentials from the Request
     * and pass them into `getUser()`
     *
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email'      => $request->request->get('email'),
            'password'   => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }


    /**
     * Get User from the database or `null` if the email not found.
     * UserProviderInterface refreshes the user at the beginning of every Request
     *
     * @param array                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        // `authenticate` can be any string but the same as has the hidden input in the Login form
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        /* TODO: Change the invalid token message with `The entered data is not verified. Try again please` */
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)
                     ->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // Fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found');
        }

        return $user;
    }


    /**
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }


    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function onAuthenticationSuccess(
        Request        $request,
        TokenInterface $token,
        $providerKey
    ): RedirectResponse {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home_index'));
    }


    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('login');
    }
}
