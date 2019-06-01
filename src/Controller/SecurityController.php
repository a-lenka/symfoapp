<?php

namespace App\Controller;

use App\DomainManager\UserDomainManager;
use App\Entity\User;
use App\Event\RegistrationEvent;
use App\Form\Handlers\UserFormHandler;
use App\Form\Models\RegistrationModel;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Security\LoginFormAuthenticator;
use App\Service\TemplateRenderer;
use Exception;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /** @var UserDomainManager */
    private $userManager;

    /** @var TemplateRenderer */
    private $renderer;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * HomeController constructor
     *
     * @param UserDomainManager        $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param TemplateRenderer         $templateRenderer
     */
    public function __construct(
        UserDomainManager        $userManager,
        EventDispatcherInterface $eventDispatcher,
        TemplateRenderer         $templateRenderer
    ) {
        $this->userManager     = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->renderer        = $templateRenderer;
    }


    /**
     * @Route("/{_locale}/login",
     *     name="login",
     *     methods="GET|POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request             $request
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function login(
        Request             $request,
        AuthenticationUtils $authenticationUtils
    ): Response {

        return new Response(
            $this->renderer->renderTemplate([
                'page'          => $this->renderer::FORM_PAGE,
                'part'          => 'security/_form-login.html.twig',
                'last_username' => $authenticationUtils->getLastUsername(),
                'error'         => $authenticationUtils->getLastAuthenticationError(),
            ], $request)
        );
    }


    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{_locale}/logout",
     *     name="logout",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"}
     * )
     *
     * @throws Exception
     */
    final public function logout(): void
    {
        throw new Exception(
            'Don\'t forget to activate logout in security.yaml'
        );
    }


    /**
     * Controller is used to manage registration of new Users
     *
     * @Route("/{_locale}/register",
     *     name="register",
     *     methods="GET|POST",
     *     requirements={"_locale": "%app_locales%"}
     * )
     *
     * @param Request                   $request
     * @param LoginFormAuthenticator    $authenticator
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserFormHandler           $formHandler
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function register(
        Request                   $request,
        LoginFormAuthenticator    $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        UserFormHandler           $formHandler
    ): Response {
        $user  = new User();
        $model = new RegistrationModel($user);

        $form = $this->createForm(RegistrationType::class, $model);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $formHandler->setRegisterFormData($form, $user);

            $this->userManager->flushUser($user);

            $this->eventDispatcher->dispatch(
                RegistrationEvent::NAME,
                new RegistrationEvent($user)
            );

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page' => $this->renderer::FORM_PAGE,
                'part' => 'security/_form-register.html.twig',
                'form' => $form->createView(),
            ], $request)
        );
    }


    /**
     * Controller is used to change User's password
     *
     * @IsGranted("ROLE_USER")
     * @Route("/{_locale}/reset",
     *     name="reset",
     *     methods="GET|POST",
     *     requirements={"_locale": "%app_locales%"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function resetPassword(Request $request): Response {
        $user = $this->getUser();

        $accessMsg = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMsg, 403); }

        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $this->userManager->setUserPassword(
                $user,
                $form['newPassword']->getData()
            );

            $this->userManager->flushUser($user);

            return $this->redirectToRoute('logout');
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page' => $this->renderer::FORM_PAGE,
                'part' => 'security/_form-reset.html.twig',
                'form' => $form->createView(),
            ], $request)
        );
    }
}
