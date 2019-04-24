<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     */
    public function showAll(): Response
    {
        $users = $this->getUserRepository()->findAll();

        return $this->render('user/list.html.twig', [
                'users' => $users,
            ]
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
}
