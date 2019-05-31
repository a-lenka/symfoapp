<?php

namespace App\Controller;

use App\DomainManager\TaskDomainManager;
use App\Entity\Task;
use App\Form\TaskType;
use App\Service\FlashSender;
use App\Service\Forms\TaskFormHandler;
use App\Service\TemplateRenderer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Manage Task Entities
 *
 * Class TaskController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    /** @var TaskDomainManager */
    private $taskManager;

    /** @var TemplateRenderer */
    private $renderer;

    /** @var FlashSender */
    private $flashSender;

    /**
     * TaskController constructor
     *
     * @param TaskDomainManager $taskManager
     * @param TemplateRenderer  $templateRenderer
     */
    public function __construct(
        TaskDomainManager $taskManager,
        TemplateRenderer  $templateRenderer,
        FlashSender       $flashSender
    ) {
        $this->taskManager = $taskManager;
        $this->renderer    = $templateRenderer;
        $this->flashSender = $flashSender;
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showTaskList(): Response
    {
        $user = $this->getUser();

        $accessMsg = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMsg, 403); }

        $userTasks = $user->getTasks();

        if(!$userTasks[0]) {
            $this->flashSender->sendNotice('It looks like you have no tasks yet');
        }

        $props = [
            'page'  => $this->renderer::LIST_PAGE,
            'part'  => 'task/_list.html.twig',
            'tasks' => $userTasks,
            'title' => 'Tasks',
            'sort_property' => 'default',
            'sort_order'    => 'default',
        ];

        return new Response(
            $this->renderer->renderTemplate($props)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showTaskListSorted(
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

        $props = [
            'page'   => $this->renderer::LIST_PAGE,
            'part'   => 'task/_list.html.twig',
            'tasks'  => $userTasks,
            'title'  => 'Tasks',
            'sort_property' => $sort_property,
            'sort_order'    => $sort_order,
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function search(Request $request, string $search_query): Response {
        if($search_query === 'empty_request') {
            return $this->redirectToRoute('task_list_all');
        }

        $result = $this->taskManager->search($this->getUser(), $search_query);

        if(!$result) {
            $this->flashSender->sendNotice(
                'It seems there are no tasks found'
            );
        }

        $props = [
            'page'  => $this->renderer::LIST_PAGE,
            'part'  => 'task/_list.html.twig',
            'tasks' => $result,
            'title' => 'Tasks',
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function confirmDeleteMultiply(Request $request): Response
    {
        $data = $request->getContent();
        $ids  = json_decode($data, true);

        $tasks = $this->taskManager->findMultiplyById($ids);

        $props = [
            'page'  => $this->renderer::CONFIRM_PAGE,
            'part'  => 'task/_confirm-delete.html.twig',
            'tasks' => $tasks,
            'title' => 'Tasks',
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showDetails(Request $request, int $id): Response
    {
        $task = $this->taskManager->findOneById($id);

        $props = [
            'page'   => $this->renderer::DETAILS_PAGE,
            'part'   => 'task/_details.html.twig',
            'task'   => $task,
            'title'  => 'Task',
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

        $props = [
            'page'  => $this->renderer::FORM_PAGE,
            'part'  => 'task/_form.html.twig',
            'task'  => $task,
            'form'  => $form->createView(),
            'title' => 'Create task',
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

        $props = [
            'page'  => $this->renderer::FORM_PAGE,
            'part'  => 'task/_form.html.twig',
            'task'  => $task,
            'form'  => $form->createView(),
            'title' => 'Create task',
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function confirmDeleteOneTask(Request $request, int $id): Response
    {
        $task = $this->taskManager->findOneById($id);

        $props = [
            'page' => $this->renderer::CONFIRM_PAGE,
            'task' => $task,
            'part' => 'task/_confirm-delete.html.twig',
        ];

        return new Response(
            $this->renderer->renderTemplate($props, $request)
        );
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
