<?php

namespace App\Controller;

use App\DomainManager\UserDomainManager;
use App\Form\AccountPropertiesType;
use App\Form\Handlers\UserFormHandler;
use App\Form\Models\AccountPropertiesModel;
use App\Service\FlashSender;
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
    /** @var UserDomainManager */
    private $userManager;

    /** @var TemplateRenderer */
    private $renderer;

    /** @var FlashSender */
    private $flashSender;

    /**
     * AccountController constructor
     *
     * @param UserDomainManager $userManager
     * @param TemplateRenderer  $templateRenderer
     * @param FlashSender       $flashSender
     */
    public function __construct(
        UserDomainManager $userManager,
        TemplateRenderer  $templateRenderer,
        FlashSender       $flashSender
    ) {
        $this->userManager = $userManager;
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
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function index(Request $request): Response
    {
        $user  = $this->getUser();
        $model = new AccountPropertiesModel($user);

        if(!$user->getTasks()[0]) {
            $this->flashSender->sendNotice(
                'Create a task to see your progress'
            );
        }

        if(!$request->attributes->get('form')) {
            $form = $this->createForm(AccountPropertiesType::class, $model, [
                'action' => $this->generateUrl('account_submit')
            ])->createView();
        } else {
            $form = $request->attributes->get('form');
        }

        $props = [
            'page' => $this->renderer::ACCOUNT_PAGE,
            'form' => $form,
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
    final public function indexSubmit(UserFormHandler $formHandler, Request $request): Response
    {
        $user  = $this->getUser();
        $model = new AccountPropertiesModel($user);

        $form = $this->createForm(AccountPropertiesType::class, $model, [
            'action' => $this->generateUrl('account_submit')
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $formHandler->setAccountFormData($form, $user);

            $this->userManager->flushUser($user);

            return $this->redirectToRoute('account');
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page' => $this->renderer::ACCOUNT_PAGE,
                'form' => $form->createView(),
            ])
        );
    }
}
