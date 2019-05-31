<?php

namespace App\Controller;

use App\Form\AccountPropertiesType;
use App\Service\FileUploader;
use App\Service\Forms\UserFormHandler;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage User's account page
 *
 * Class AccountController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/{_locale}/account",
     *     name="account",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @return Response
     */
    final public function index(): Response
    {
        $user = $this->getUser();

        $notice = 'Create a task to see your progress';
        if(!$user->getTasks()[0]) {$this->addFlash('notice', $notice); }

        $form = $this->createForm(AccountPropertiesType::class, $user, [
            'action' => $this->generateUrl('account_submit')
        ]);

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/account/submit",
     *     name="account_submit",
     *     methods="POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param FileUploader    $fileUploader
     * @param UserFormHandler $formHandler
     * @param Request         $request
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function indexSubmit(
        FileUploader    $fileUploader,
        UserFormHandler $formHandler,
        Request         $request
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(AccountPropertiesType::class, $user, [
            'action' => $this->generateUrl('account_submit')
        ]);

        if($formHandler->handle(
            $request, $form, $fileUploader, $user)
        ) {
            return $this->redirectToRoute('account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
