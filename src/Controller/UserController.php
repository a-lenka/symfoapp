<?php

namespace App\Controller;

use App\DomainManager\UserManager;
use App\Entity\User;
use App\Form\UserType;
use App\Service\Forms\UserFormHandler;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage User Entities
 *
 * Class UserController
 * @package App\Controller
 * @IsGranted("ROLE_ROOT")
 */
class UserController extends AbstractController
{
    /** @var UserManager */
    private $userManager;

    /** @var UserFormHandler */
    private $formHandler;

    /**
     * UserController constructor
     *
     * @param UserManager     $userManager
     * @param UserFormHandler $formHandler
     */
    public function __construct(UserManager $userManager, UserFormHandler $formHandler)
    {
        $this->userManager = $userManager;
        $this->formHandler = $formHandler;
    }

    /**
     * Returns only part of a template with a form
     * to be inserted into a modal window (for Ajax Requests),
     * or an entire page with a form inside
     * to redirect or navigate through browser history
     *
     * @param Request $request
     * @param string  $page
     * @param string  $part
     *
     * @return string
     */
    private function chooseTemplate(Request $request, string $page, string $part): string
    {
        return $request->isXmlHttpRequest() ? $part : $page;
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
     */
    final public function showAll(): Response
    {
        $users = $this->userManager->findAll();

        return $this->render('list.html.twig', [
            'users'     => $users,
            'title'     => 'Users',
            'list_part' => 'user/_list.html.twig',
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
     *     requirements={
     *          "_locale": "%app_locales%",
     *          "sort_property"="id|email|roles|tasks",
     *          "sort_order"="asc|desc|default"
     *      },
     * )
     *
     * @param Request $request
     * @param string  $sort_property - Property to sort
     * @param string  $sort_order    - Sorting order
     *
     * @return Response
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

        $page = 'list.html.twig';
        $part = 'user/_list.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'users'     => $allUsers,
            'title'     => 'Users',
            'list_part' => $part,
            'sort_property' => $sort_property,
            'sort_order'    => $sort_order,
        ]);
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
     */
    final public function confirmDeleteMultiply(Request $request): Response
    {
        $data = $request->getContent();
        $ids  = json_decode($data, true);

        $users = $this->userManager->findMultiplyById($ids);

        $page = 'confirm.html.twig';
        $part = 'user/_confirm-delete.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'users'     => $users,
            'title'     => 'Users',
            'confirm_part'  => $part,
        ]);
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
        $data = $request->getContent();
        $ids  = json_decode($data, false);

        $this->userManager->deleteMultiplyById($ids);

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
     */
    final public function search(Request $request, string $search_query): Response
    {
        if($search_query === 'empty_request') {
            return $this->redirectToRoute('user_list_all');
        }

        $result = $this->userManager->search($search_query);

        $notice = 'It seems there are no users found';
        if(!$result) { $this->addFlash('notice', $notice); }

        $page = 'list.html.twig';
        $part = 'user/_list.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'users'     => $result,
            'title'     => 'Users',
            'list_part' => $part,
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
     * @param integer $id      - User id from request params
     *
     * @return Response
     */
    final public function showDetails(Request $request, int $id): Response
    {
        $user = $this->userManager->findOneById($id);

        $page = 'details.html.twig';
        $part = 'user/_details.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'user'      => $user,
            'entity'    => $user, // For common `details`
            'title'     => 'User',
            'details_part' => $part,
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
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function updateUser(Request $request, int $id): Response
    {
        $user = $this->userManager->findOneById($id);

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('user_update', ['id' => $id]),
        ]);

        if($this->formHandler->handle($request, $form, $user)) {
            return $this->redirectToRoute('user_list_all');
        }

        $page = 'form.html.twig';
        $part = 'user/_form.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'user' => $user,
            'form' => $form->createView(),
            'form_part' => $part,
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
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function createUser(Request $request): Response
    {
        $user = new User();
        $user->setTheme($this->userManager::DEFAULT_THEME);

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('user_create'),
        ]);

        if($this->formHandler->handle($request, $form, $user)) {
            return $this->redirectToRoute('user_list_all');
        }

        $page = 'form.html.twig';
        $part = 'user/_form.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'user' => $user,
            'form' => $form->createView(),
            'form_part' => $part,
            'title'     => 'Create user'
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
    final public function confirmDeleteUser(Request $request, int $id): Response
    {
        $user = $this->userManager->findOneById($id);

        $page = 'confirm.html.twig';
        $part = 'user/_confirm-delete.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'user' => $user,
            'confirm_part' => $part,
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
     * @throws FileNotFoundException
     */
    final public function deleteUser(int $id): Response
    {
        $this->userManager->deleteOneById($id);

        return $this->redirectToRoute('user_list_all');
    }
}
