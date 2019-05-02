<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
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
     * @return Response
     */
    public function showAll(): Response
    {
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();

        return $this->render('list.html.twig', [
            'tasks'     => $tasks,
            'title'     => 'Tasks',
            'list_part' => 'task/_list.html.twig',
            'sort_property' => 'default',
            'sort_order'    => 'default',
        ]);
    }


    /**
     * Show sorted Task entities
     *
     * @Route("/{_locale}/task/list/all/sorted/{sort_property}/{sort_order}",
     *     name="task_list_sorted",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={
     *          "_locale": "%app_locales%",
     *          "sort_property"="id|title|dateDeadline|state",
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
    public function showSorted(Request $request, string $sort_property, string $sort_order): Response
    {
        $allTasks = $this->getDoctrine()->getRepository(Task::class)->sortByProperty(
            $sort_property, $sort_order
        );

        $listPart = 'task/_list.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $listPart
            : 'list.html.twig';

        return $this->render($template, [
            'tasks'     => $allTasks,
            'title'     => 'Tasks',
            'list_part' => $listPart,
            'sort_property' => $sort_property,
            'sort_order'    => $sort_order,
        ]);
    }


    /**
     * Show details page for one separate Task
     *
     * @Route("/{_locale}/task/{id}/details",
     *     name="task_details",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param integer $id - Task id from request params
     *
     * @return Response
     */
    public function showDetails(Request $request, int $id): Response
    {
        $task     = $this->getDoctrine()->getRepository(Task::class)->find($id);
        $template = $request->isXmlHttpRequest()
            ? 'task/_details.html.twig'
            : 'task/details.html.twig';

        return $this->render($template, [
            'task' => $task,
        ]);
    }


    /**
     * Create Task Entity
     *
     * @Route("/{_locale}/task/create",
     *     name="task_create",
     *     methods="GET|POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createTask(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('task_create'),
        ]);

        $form->handleRequest($request);

        if ($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_list_all');
        }

        $formPart = 'task/_form.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $formPart
            : 'form.html.twig';

        return $this->render($template, [
            'task'  => $task,
            'title' => 'Create task',
            'form'  => $form->createView(),
            'form_part' => $formPart,
        ]);
    }


    /**
     * Update Task Entity
     *
     * @Route("/{_locale}/task/{id}/update",
     *     name="task_update",
     *     methods="GET|POST",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function updateTask(Request $request, int $id): Response
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('task_update', ['id' => $id]),
        ]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')
            && $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_list_all');
        }

        $formPart = 'task/_form.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $formPart
            : 'form.html.twig';

        return $this->render($template, [
            'task' => $task,
            'form' => $form->createView(),
            'form_part' => $formPart,
            'title' => 'Update task',
        ]);
    }


    /**
     * Confirm deleting Task Entity
     *
     * @Route("/{_locale}/task/{id}/delete/confirm",
     *     name="task_delete_confirm",
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
    public function confirmDeleteTask(Request $request, int $id): Response
    {
        $task     = $this->getDoctrine()->getRepository(Task::class)->find($id);
        $template = $request->isXmlHttpRequest()
            ? 'task/_confirm-delete.html.twig'
            : 'confirm.html.twig';

        return $this->render($template, [
            'task' => $task,
            'confirm_part' => 'task/_confirm-delete.html.twig',
        ]);
    }


    /**
     * Delete Task Entity permanently
     *
     * @Route("/{_locale}/task/{id}/delete",
     *     name="task_delete",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param integer $id
     *
     * @return Response
     */
    public function deleteTask(int $id): Response
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('task_list_all');
    }
}
