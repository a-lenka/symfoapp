<?php

namespace App\Controller;

use App\Form\AccountPropertiesType;
use App\Service\FlashSender;
use App\Service\Forms\UserFormHandler;
use App\Service\TemplateRenderer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Manage User's account page
 *
 * Class AccountController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /** @var TemplateRenderer */
    private $renderer;

    /** @var FlashSender */
    private $flashSender;

    /**
     * AccountController constructor
     *
     * @param TemplateRenderer $templateRenderer
     * @param FlashSender      $flashSender
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        FlashSender      $flashSender
    ) {
        $this->renderer    = $templateRenderer;
        $this->flashSender = $flashSender;
    }


    /**
     * @Route("/{_locale}/account",
     *     name="account",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function index(): Response
    {
        $user = $this->getUser();

        if(!$user->getTasks()[0]) {
            $this->flashSender->sendNotice(
                'Create a task to see your progress'
            );
        }

        $form = $this->createForm(AccountPropertiesType::class, $user, [
            'action' => $this->generateUrl('account_submit')
        ]);

        $props = [
            'page' => $this->renderer::ACCOUNT_PAGE,
            'form' => $form->createView(),
        ];

        return new Response(
            $this->renderer->renderTemplate($props)
        );
    }


    /**
     * @Route("/{_locale}/account/submit",
     *     name="account_submit",
     *     methods="POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param UserFormHandler $formHandler
     * @param Request         $request
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function indexSubmit(
        UserFormHandler $formHandler,
        Request         $request
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(AccountPropertiesType::class, $user, [
            'action' => $this->generateUrl('account_submit')
        ]);

        if($formHandler->handle(
            $request, $form, $user)
        ) {
            return $this->redirectToRoute('account');
        }

        $props = [
            'page' => $this->renderer::ACCOUNT_PAGE,
            'form' => $form->createView(),
        ];

        return new Response(
            $this->renderer->renderTemplate($props)
        );
    }
}
