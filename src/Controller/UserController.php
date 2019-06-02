<?php

namespace App\Controller;

use App\DomainManager\UserDomainManager;
use App\Entity\User;
use App\Form\Handlers\UserFormHandler;
use App\Form\Models\UserTypeModel;
use App\Form\UserType;
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
 * Manage User Entities
 *
 * Class UserController
 * @package App\Controller
 * @IsGranted("ROLE_ROOT")
 */
class UserController extends AbstractController
{
    /** @var UserDomainManager */
    private $userManager;

    /** @var UserFormHandler */
    private $formHandler;

    /** @var TemplateRenderer */
    private $renderer;

    /** @var FlashSender */
    private $flashSender;

    /**
     * UserController constructor
     *
     * @param UserDomainManager $userManager
     * @param UserFormHandler   $formHandler
     * @param TemplateRenderer  $templateRenderer
     * @param FlashSender       $flashSender
     */
    public function __construct(
        UserDomainManager $userManager,
        UserFormHandler   $formHandler,
        TemplateRenderer  $templateRenderer,
        FlashSender       $flashSender
    ) {
        $this->userManager = $userManager;
        $this->formHandler = $formHandler;
        $this->renderer    = $templateRenderer;
        $this->flashSender = $flashSender;
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
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showAll(): Response
    {
        return new Response(
            $this->renderer->renderTemplate([
                'page'      => $this->renderer::LIST_PAGE,
                'users'     => $this->userManager->findAll(),
                'title'     => 'Users',
                'part'          => 'user/_list.html.twig',
                'sort_property' => 'default',
                'sort_order'    => 'default',
            ])
        );
    }


    /**
     * Show sorted User entities
     *
     * @Route("/{_locale}/user/list/all/sorted/{sort_property}/{sort_order}",
     *     name="user_list_sorted",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={
     *          "_locale": "%app_locales%",
     *          "sort_property"="id|nickname|email|roles|tasks",
     *          "sort_order"="asc|desc|default"
     *      },
     * )
     *
     * @param Request $request
     * @param string  $sort_property - Property to sort
     * @param string  $sort_order    - Sorting order
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showSorted(
        Request $request,
        string  $sort_property,
        string  $sort_order
    ): Response {
        if($sort_property === 'tasks') {
            $allUsers = $this->userManager->findAll();

            if($sort_order === 'asc') {
                $allUsers = $this->userManager->sortAscByNumberOfTasks($allUsers);
            }

            if($sort_order === 'desc') {
                $allUsers = $this->userManager->sortDescByNumberOfTasks($allUsers);
            }
        } else {
            $allUsers = $this->userManager->sortByProperty($sort_property, $sort_order);
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page' => $this->renderer::LIST_PAGE,
                'part' => 'user/_list.html.twig',
                'users'     => $allUsers,
                'title'     => 'Users',
                'sort_property' => $sort_property,
                'sort_order'    => $sort_order,
            ], $request)
        );
    }


    /**
     * Confirm delete multiply User Entity
     *
     * @Route("/{_locale}/user/list/confirm",
     *     name="user_list_confirm",
     *     methods="POST",
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
    final public function confirmDeleteMultiply(Request $request): Response
    {
        $ids = json_decode($request->getContent(), true);

        return new Response(
            $this->renderer->renderTemplate( [
                'page'  => $this->renderer::CONFIRM_PAGE,
                'users' => $this->userManager->findMultiplyById($ids),
                'title' => 'Users',
                'part'  => 'user/_confirm-delete.html.twig',
            ], $request)
        );
    }


    /**
     * Delete multiply User Entity
     *
     * @Route("/{_locale}/user/list/delete",
     *     name="user_list_delete",
     *     methods="POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws FileNotFoundException
     */
    final public function deleteMultiply(Request $request): Response
    {
        $this->userManager->deleteMultiplyById(
            json_decode($request->getContent(), false)
        );

        return $this->redirectToRoute('user_list_all');
    }


    /**
     * Show founded users
     *
     * @Route("/{_locale}/user/list/search/{search_query}",
     *     name="user_search",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param string  $search_query
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function search(Request $request, string $search_query): Response
    {
        if($search_query === 'empty_request') {
            return $this->redirectToRoute('user_list_all');
        }

        $result = $this->userManager->search($search_query);

        if(!$result) {
            $this->flashSender->sendNoUsersFound();
        }

        return new Response(
            $this->renderer->renderTemplate( [
                'page'  => $this->renderer::LIST_PAGE,
                'part'  => 'user/_list.html.twig',
                'users' => $result,
                'title' => 'Users',
            ], $request)
        );
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
     * @param integer $id      - User id from request params
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showDetails(Request $request, int $id): Response
    {
        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::DETAILS_PAGE,
                'user'  => $this->userManager->findOneById($id),
                'title' => 'User',
                'part'  => 'user/_details.html.twig',
            ], $request)
        );
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
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function updateUser(Request $request, int $id): Response
    {
        $user  = $this->userManager->findOneById($id);
        $model = new UserTypeModel($user);

        $form = $this->createForm(UserType::class, $model, [
            'action' => $this->generateUrl('user_update', ['id' => $id]),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->formHandler->setUpdateFormData($form, $user);

            $this->userManager->flushUser($user);

            return $this->redirectToRoute('user_list_all');
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::FORM_PAGE,
                'user'  => $user,
                'form'  => $form->createView(),
                'part'  => 'user/_form.html.twig',
                'title' => 'Update user',
            ], $request)
        );
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
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function createUser(Request $request): Response
    {
        $user  = new User();
        $model = new UserTypeModel($user);

        $form = $this->createForm(UserType::class, $model, [
            'action' => $this->generateUrl('user_create'),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->formHandler->setCreateFormData($form, $user);

            $this->userManager->flushUser($user);

            return $this->redirectToRoute('user_list_all');
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::FORM_PAGE,
                'user'  => $user,
                'form'  => $form->createView(),
                'part'  => 'user/_form.html.twig',
                'title' => 'Create user'
            ], $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return Response
     */
    final public function confirmDeleteUser(Request $request, int $id): Response
    {
        return new Response(
            $this->renderer->renderTemplate([
                'page' => $this->renderer::CONFIRM_PAGE,
                'part' => 'user/_confirm-delete.html.twig',
                'user' => $this->userManager->findOneById($id),
            ], $request)
        );
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
     * @throws FileNotFoundException
     */
    final public function deleteUser(int $id): Response
    {
        $this->userManager->deleteOneById($id);

        return $this->redirectToRoute('user_list_all');
    }
}
