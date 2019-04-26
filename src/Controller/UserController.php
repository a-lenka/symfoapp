<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Manage User Entities
 *
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * Get User Repository for convenient work with User Entity
     *
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        return $this->getDoctrine()
            ->getRepository(User::class);
    }


    /**
     * Show list of all User entities
     *
     * @Route("/{_locale}/user/list/all",
     *     name="user_list_all",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAll(Request $request): Response
    {
        $users = $this->getUserRepository()->findAll();

        $listPart = 'user/_list.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $listPart
            : 'list.html.twig';

        return $this->render($template, [
            'users'     => $users,
            'list_part' => $listPart,
            'sort_property' => 'default',
            'sort_order'    => 'default',
        ]);
    }


    /**
     * Show sorted User entities
     *
     * @Route("/{_locale}/user/list/all/sorted/{sort_property}/{sort_order}",
     *     name="user_list_sorted",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%", "sort_property"="id|email|roles", "sort_order"="asc|desc|default"},
     * )
     *
     * @param Request $request
     * @param int     $sort_property - Property to sort related tasks
     * @param int     $sort_order    - Sorting order
     *
     * @return Response
     */
    public function showSorted(Request $request, $sort_property, $sort_order): Response
    {
        $allUsers = $this->getUserRepository()->sortByProperty(
            $sort_property, $sort_order
        );

        $listPart = 'user/_list.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $listPart
            : 'list.html.twig';

        return $this->render($template, [
            'users'     => $allUsers,
            'list_part' => $listPart,
            'sort_property' => $sort_property,
            'sort_order'    => $sort_order,
        ]);
    }


    /**
     * Show details page for one separate User
     *
     * @Route("/{_locale}/user/{id}/details",
     *     name="user_details",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param integer $id - User id from request params
     *
     * @return Response
     */
    public function showDetails(Request $request, $id): Response
    {
        $user     = $this->getUserRepository()->find($id);
        $template = $request->isXmlHttpRequest()
            ? 'user/_details.html.twig'
            : 'user/details.html.twig';

        return $this->render($template, [
            'user' => $user,
        ]);
    }


    /**
     * Update User Entity
     *
     * @Route("/{_locale}/user/{id}/update",
     *     name="user_update",
     *     methods="GET|POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param integer $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function updateUser(
        $id,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {

        $user = $this->getUserRepository()->find($id);

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('user_update', ['id' => $id]),
        ]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $password = $passwordEncoder->encodePassword(
                $user, $user->getPassword()
            );

            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_list_all');
        }

        $formPart = 'user/_form.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $formPart
            : 'form.html.twig';

        return $this->render($template, [
            'user' => $user,
            'form' => $form->createView(),
            'form_part' => $formPart,
            'title' => 'Update user',
        ]);
    }


    /**
     * Create User Entity
     *
     * @Route("/{_locale}/user/create",
     *     name="user_create",
     *     methods="GET|POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function createUser(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {

        $user = new User();

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('user_create'),
        ]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $password = $passwordEncoder->encodePassword(
                $user, $user->getPassword()
            );
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_list_all');
        }

        $formPart = 'user/_form.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $formPart
            : 'form.html.twig';

        return $this->render($template, [
            'user' => $user,
            'form' => $form->createView(),
            'form_part' => $formPart,
            'title' => 'Create user'
        ]);
    }


    /**
     * Confirm deleting User Entity
     *
     * @Route("/{_locale}/user/{id}/delete/confirm",
     *     name="user_delete_confirm",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function confirmDeleteUser(Request $request, $id): Response
    {
        $user     = $this->getUserRepository()->find($id);
        $template = $request->isXmlHttpRequest()
            ? 'user/_confirm-delete.html.twig'
            : 'confirm.html.twig';

        return $this->render($template, [
            'user' => $user,
            'confirm_part' => 'user/_confirm-delete.html.twig',
        ]);
    }


    /**
     * Delete User Entity permanently
     *
     * @Route("/{_locale}/user/{id}/delete",
     *     name="user_delete",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param integer $id
     *
     * @return Response
     */
    public function deleteUser($id): Response
    {
        $user = $this->getUserRepository()->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_list_all');
    }
}
