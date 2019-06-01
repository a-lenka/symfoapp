<?php

namespace App\Controller;

use App\DomainManager\TaskDomainManager;
use App\Entity\Task;
use App\Form\Handlers\TaskFormHandler;
use App\Form\Models\TaskTypeModel;
use App\Form\TaskType;
use App\Service\FlashSender;
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

    /** @var TaskFormHandler */
    private $formHandler;

    /** @var TemplateRenderer */
    private $renderer;

    /** @var FlashSender */
    private $flashSender;

    /**
     * TaskController constructor
     *
     * @param TaskDomainManager $taskManager
     * @param TaskFormHandler   $formHandler
     * @param TemplateRenderer  $templateRenderer
     * @param FlashSender       $flashSender
     */
    public function __construct(
        TaskDomainManager $taskManager,
        TaskFormHandler   $formHandler,
        TemplateRenderer  $templateRenderer,
        FlashSender       $flashSender
    ) {
        $this->taskManager = $taskManager;
        $this->formHandler = $formHandler;
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

        if(!$user) {
            throw new AccessDeniedException(
                'Login please. You can access this page only from your account',
                403
            );
        }

        $tasks = $user->getTasks();

        if(!$tasks[0]) {
            $this->flashSender->sendNotice(
                'It looks like you have no tasks yet'
            );
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::LIST_PAGE,
                'part'  => 'task/_list.html.twig',
                'tasks' => $tasks,
                'title' => 'Tasks',
                'sort_property' => 'default',
                'sort_order'    => 'default',
            ])
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

        if(!$user) {
            throw new AccessDeniedException(
                'Login please. You can access this page only from your account',
                403
            );
        }

        $tasks = $this->taskManager->sort(
            $user,
            $sort_property,
            $sort_order
        );

        if(empty($tasks)) {
            throw new NotFoundHttpException(
                'It seems there are no tasks found. Do you want to create the new one?'
            );
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'   => $this->renderer::LIST_PAGE,
                'part'   => 'task/_list.html.twig',
                'tasks'  => $tasks,
                'title'  => 'Tasks',
                'sort_property' => $sort_property,
                'sort_order'    => $sort_order,
            ], $request)
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

        $founded = $this->taskManager->search(
            $this->getUser(),
            $search_query
        );

        if(!$founded) {
            $this->flashSender->sendNotice(
                'It seems there are no tasks found'
            );
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::LIST_PAGE,
                'part'  => 'task/_list.html.twig',
                'tasks' => $founded,
                'title' => 'Tasks',
            ], $request)
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
        $tasks = $this->taskManager->findMultiplyById(
            json_decode($request->getContent(), true)
        );

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::CONFIRM_PAGE,
                'part'  => 'task/_confirm-delete.html.twig',
                'tasks' => $tasks,
                'title' => 'Tasks',
            ], $request)
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
        $this->taskManager->deleteMultiplyById(
            json_decode($request->getContent(), false)
        );

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

        return new Response(
            $this->renderer->renderTemplate([
                'page'   => $this->renderer::DETAILS_PAGE,
                'part'   => 'task/_details.html.twig',
                'task'   => $task,
                'title'  => 'Task',
            ], $request)
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
     * @param Request $request
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function createTask(Request $request): Response {
        $task  = new Task();
        $model = new TaskTypeModel($task);

        $form = $this->createForm(TaskType::class, $model, [
            'action' => $this->generateUrl('task_create'),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $task = $this->formHandler->setCreateFormData(
                $form,
                $task,
                $this->getUser()
            );

            $this->taskManager->flushTask($task);

            return $this->redirectToRoute('task_list_all');
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::FORM_PAGE,
                'part'  => 'task/_form.html.twig',
                'task'  => $task,
                'form'  => $form->createView(),
                'title' => 'Create task',
            ], $request)
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
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function updateTask(Request $request, int $id): Response
    {
        $task  = $this->taskManager->findOneById($id);
        $model = new TaskTypeModel($task);

        $form = $this->createForm(TaskType::class, $model, [
            'action' => $this->generateUrl('task_update', [
                'id' => $id
            ]),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $task = $this->formHandler->setUpdateFormData(
                $form,
                $task,
                $this->getUser()
            );

            $this->taskManager->flushTask($task);

            return $this->redirectToRoute('task_list_all');
        }

        return new Response(
            $this->renderer->renderTemplate([
                'page'  => $this->renderer::FORM_PAGE,
                'part'  => 'task/_form.html.twig',
                'task'  => $task,
                'form'  => $form->createView(),
                'title' => 'Create task',
            ], $request)
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

        return new Response(
            $this->renderer->renderTemplate( [
                'page' => $this->renderer::CONFIRM_PAGE,
                'task' => $task,
                'part' => 'task/_confirm-delete.html.twig',
            ], $request)
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
