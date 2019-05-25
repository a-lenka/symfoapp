<?php

namespace App\Controller;

use App\Form\AccountPropertiesType;
use App\Service\FileUploader;
use App\Service\PathKeeper;
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

        if(!$user->getTasks()[0]) {
            $this->addFlash(
                'notice',
                'Create a task to see your progress'
            );
        }

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
     * @param FileUploader $uploader
     * @param PathKeeper   $pathKeeper
     * @param Request      $request
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function indexSubmit(FileUploader $uploader, PathKeeper $pathKeeper, Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountPropertiesType::class, $user, [
            'action' => $this->generateUrl('account_submit')
        ]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $avatar = $form['avatar']->getData();

            if ($avatar) {
                $newName = $uploader->uploadEntityIcon(
                    PathKeeper::UPLOADED_AVATARS_DIR,
                    $avatar,
                    $user->getAvatar()
                );

                $user->setAvatar($newName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
