<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage Task Entities
 *
 * Class TaskController
 * @package App\Controller
 */
class TaskController extends AbstractController
{
    /**
     * Show list of all Task entities
     *
     * @Route("/{_locale}/task/list/all",
     *     name="task_list_all",
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
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();

        return $this->render('list.html.twig', [
            'tasks'     => $tasks,
            'title'     => 'Tasks',
            'list_part' => 'task/_list.html.twig',
        ]);
    }
}
