<?php

namespace App\Controller;

use App\DomainManager\TaskManager;
use App\Entity\Task;
use App\Form\TaskType;
use App\Service\Forms\TaskFormHandler;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage Task Entities
 *
 * Class TaskController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    /** @var TaskManager */
    private $taskManager;

    /**
     * TaskController constructor
     *
     * @param TaskManager $taskManager
     */
    public function __construct(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
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
    final public function showAll(): Response
    {
        $user = $this->getUser();

        $accessMsg = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMsg, 403); }

        $userTasks = $user->getTasks();

        $notice = 'It looks like you have no tasks yet';
        if(!$userTasks[0]) { $this->addFlash('notice', $notice); }

        return $this->render('list.html.twig', [
            'tasks'     => $userTasks,
            'title'     => 'Tasks',
            'list_part' => 'task/_list.html.twig',
            'sort_property' => 'default',
            'sort_order'    => 'default',
        ]);
    }


    /**
     * Confirm delete multiply Task Entities
     *
     * @Route("/{_locale}/task/list/confirm",
     *     name="task_list_confirm",
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

        $tasks = $this->taskManager->findMultiplyById($ids);

        $page = 'confirm.html.twig';
        $part = 'task/_confirm-delete.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'tasks'     => $tasks,
            'title'     => 'Tasks',
            'confirm_part'  => $part,
        ]);
    }


    /**
     * Delete multiply Task Entity
     *
     * @Route("/{_locale}/task/list/delete",
     *     name="task_list_delete",
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

        $this->taskManager->deleteMultiplyById($ids);

        return $this->redirectToRoute('task_list_all');
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
    final public function showSorted(
        Request $request,
        string  $sort_property,
        string  $sort_order
    ): Response {
        $user = $this->getUser();

        $accessMsg = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMsg, 403); }

        $userTasks = $this->taskManager->sort(
            $user, $sort_property, $sort_order
        );

        $notFoundMsg = 'It seems there are no tasks found. Do you want to create the new one?';
        if(empty($userTasks)) { throw new NotFoundHttpException($notFoundMsg); }

        $page = 'list.html.twig';
        $part = 'task/_list.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'tasks'     => $userTasks,
            'title'     => 'Tasks',
            'list_part' => $part,
            'sort_property' => $sort_property,
            'sort_order'    => $sort_order,
        ]);
    }


    /**
     * Show founded tasks
     *
     * @Route("/{_locale}/task/list/search/{search_query}",
     *     name="task_search",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @param Request $request
     * @param string  $search_query
     *
     * @return Response
     */
    final public function search(Request $request, string $search_query): Response {
        if($search_query === 'empty_request') {
            return $this->redirectToRoute('task_list_all');
        }

        $result = $this->taskManager->search($this->getUser(), $search_query);

        $notice = 'It seems there are no tasks found';
        if(!$result) { $this->addFlash('notice', $notice); }

        $page = 'list.html.twig';
        $part = 'task/_list.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'tasks'     => $result,
            'title'     => 'Tasks',
            'list_part' => $part,
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
     * @param integer $id      - Task id from request params
     *
     * @return Response
     */
    final public function showDetails(Request $request, int $id): Response
    {
        $task = $this->taskManager->findOneById($id);

        $page = 'details.html.twig';
        $part = 'task/_details.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'task'   => $task,
            'entity' => $task, // For common `details`
            'title'  => 'Task',
            'details_part' => $part,
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
     * @param Request         $request
     * @param TaskFormHandler $formHandler
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function createTask(Request $request, TaskFormHandler $formHandler): Response {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('task_create'),
        ]);

        if($formHandler->handle(
            $request, $form, $task, $this->getUser())
        ) {
            return $this->redirectToRoute('task_list_all');
        }

        $page = 'form.html.twig';
        $part = 'task/_form.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'task'      => $task,
            'title'     => 'Create task',
            'form'      => $form->createView(),
            'form_part' => $part,
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
     * @param Request         $request
     * @param TaskFormHandler $formHandler
     * @param integer         $id
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function updateTask(
        Request         $request,
        TaskFormHandler $formHandler,
        int $id
    ): Response {

        $task = $this->taskManager->findOneById($id);

        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('task_update', [
                'id' => $id
            ]),
        ]);

        if($formHandler->handle(
            $request, $form, $task, $this->getUser())
        ) {
            return $this->redirectToRoute('task_list_all');
        }

        $page = 'form.html.twig';
        $part = 'task/_form.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'task' => $task,
            'form' => $form->createView(),
            'form_part' => $part,
            'title'     => 'Update task',
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
    final public function confirmDeleteTask(Request $request, int $id): Response
    {
        $task = $this->taskManager->findOneById($id);

        $page = 'confirm.html.twig';
        $part = 'task/_confirm-delete.html.twig';
        $template = $this->chooseTemplate($request, $page, $part);

        return $this->render($template, [
            'task'         => $task,
            'confirm_part' => $part,
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
     * @throws FileNotFoundException
     */
    final public function deleteTask(int $id): Response
    {
        $this->taskManager->deleteOneById($id);

        return $this->redirectToRoute('task_list_all');
    }
}
