<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\RegistrationEvent;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Security\LoginFormAuthenticator;
use App\Service\Forms\UserFormHandler;
use App\Service\MailSender;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
    /** @var TemplateRenderer */
    private $renderer;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * HomeController constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param TemplateRenderer         $templateRenderer
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TemplateRenderer         $templateRenderer
    ) {
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
    final public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $props = [
            'page'          => $this->renderer::FORM_PAGE,
            'part'          => 'security/_form-login.html.twig',
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
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
     * @throws \Exception
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
     * @param MailSender                $mailSender
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
        UserFormHandler           $formHandler,
        MailSender                $mailSender
    ): Response {
        $user = new User();
        $user->setTheme('red lighten-2');

        $form = $this->createForm(RegistrationType::class, $user);

        if($formHandler->handle($request, $form, $user)) {

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

        $props = [
            'page' => $this->renderer::FORM_PAGE,
            'part' => 'security/_form-register.html.twig',
            'form' => $form->createView(),
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
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
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function resetPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();

        $accessMsg = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMsg, 403); }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('logout');
        }

        $props = [
            'page' => $this->renderer::FORM_PAGE,
            'part' => 'security/_form-reset.html.twig',
            'form' => $form->createView(),
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
    }
}
