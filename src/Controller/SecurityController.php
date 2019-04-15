<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
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
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $template = $request->isXmlHttpRequest()
            ? 'security/_login_form.html.twig'
            : 'security/login.html.twig';

        return $this->render($template, [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }


    /**
     * @Route("/{_locale}/logout",
     *     name="logout",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"}
     * )
     *
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new Exception(
            'Don\'t forget to activate logout in security.yaml'
        );
    }
}
