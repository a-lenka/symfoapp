<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Security\LoginFormAuthenticator;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * Returns only part of a template with a form
     * to be inserted into a modal window (for Ajax Requests),
     * or an entire page with a form inside
     * to redirect or navigate through browser history
     *
     * @param Request   $request
     * @param $formPart $string
     *
     * @return string
     */
    private function chooseTemplate(Request $request, string $formPart): string
    {
        return $request->isXmlHttpRequest()
            ? $formPart
            : 'form.html.twig';
    }


    /**
     * @Route("/{_locale}/login",
     *     name="login",
     *     methods="GET|POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    final public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $formPart = 'security/_form-login.html.twig';
        $template = $this->chooseTemplate($request, $formPart);

        return $this->render($template, [
            'form_part'     => $formPart,
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
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
     * @param GuardAuthenticatorHandler    $guardHandler
     * @param LoginFormAuthenticator       $authenticator
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    final public function register(
        LoginFormAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        $formPart = 'security/_form-register.html.twig';
        $template = $this->chooseTemplate($request, $formPart);

        return $this->render($template, [
            'form_part' => $formPart,
            'form'      => $form->createView(),
        ]);
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
     */
    final public function resetPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();

        if(!$user) {
            throw new AccessDeniedException(
            /** TODO: Secure the link from non authenticated Users instead */
                'Login please. You can access this page only from your account.', 403
            );
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('logout');
        }

        $formPart = 'security/_form-reset.html.twig';
        $template = $this->chooseTemplate($request, $formPart);

        return $this->render($template, [
            'form_part' => $formPart,
            'form'      => $form->createView(),
        ]);
    }
}
