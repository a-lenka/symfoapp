<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
    private function chooseTemplate($request, $formPart): string
    {
        return $request->isXmlHttpRequest()
            ? $formPart
            : 'security/form-page.html.twig';
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
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
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


    /**
     * Controller is used to manage registration of new Users
     *
     * @Route("/{_locale}/register",
     *     name="register",
     *     methods="GET|POST",
     *     requirements={"_locale": "%app_locales%"}
     * )
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function register(
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

            return $this->redirectToRoute('home_index');
        }

        $formPart = 'security/_form-register.html.twig';
        $template = $this->chooseTemplate($request, $formPart);

        return $this->render($template, [
            'form_part' => $formPart,
            'form'      => $form->createView(),
        ]);
    }
}
