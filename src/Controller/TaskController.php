<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\FileUploader;
use App\Service\PathKeeper;
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

        if(!$user) {
            throw new AccessDeniedException(
                /** TODO: Secure the link from non authenticated Users instead */
                'Login please. You can access this page only from your account.', 403
            );
        }

        $userTasks = $user->getTasks();

        if(!$userTasks[0]) {
            $this->addFlash(
                'notice',
                'It seems there are no tasks found'
            );
        }

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
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = [];

        $data = $request->getContent();
        $ids  = json_decode($data, true);

        foreach((array)$ids as $id) {
            $task    = $repository->findOneBy(['id' => $id]);
            $tasks[] = $task;
        }

        $confirm_part = 'task/_confirm-delete.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $confirm_part
            : 'confirm.html.twig';

        return $this->render($template, [
            'tasks'     => $tasks,
            'title'     => 'Tasks',
            'confirm_part'  => $confirm_part,
            'sort_property' => 'default',
            'sort_order'    => 'default',
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
     * @param Request      $request
     * @param FileUploader $uploader
     *
     * @return Response
     * @throws FileNotFoundException
     */
    final public function deleteMultiply(Request $request, FileUploader $uploader): Response
    {
        $repository    = $this->getDoctrine()->getRepository(Task::class);
        $entityManager = $this->getDoctrine()->getManager();

        $data = $request->getContent();
        $ids  = json_decode($data, false);

        foreach((array) $ids as $id) {
            $task = $repository->findOneBy(['id' => $id]);

            $iconName = $task->getIcon();
            $uploader->deleteAvatar($iconName);

            $entityManager->remove($task);
        }

        $entityManager->flush();
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
    final public function showSorted(Request $request, string $sort_property, string $sort_order): Response
    {
        $user = $this->getUser();

        if(!$user) {
            throw new AccessDeniedException(
                /** TODO: Secure the link from non authenticated Users instead */
                'Login please. You can access this page only from your account.', 403
            );
        }

        $userTasks = $this->getDoctrine()->getRepository(Task::class)->sortByProperty(
            $user->getId(), $sort_property, $sort_order
        );

        if(!$userTasks[0]) {
            throw new NotFoundHttpException(
                /** TODO: Show some template instead. User is not obliged to have tasks */
                'It seems there are no tasks found. Do you want to create the new one?'
            );
        }

        $listPart = 'task/_list.html.twig';
        $template = $request->isXmlHttpRequest()
            ? $listPart
            : 'list.html.twig';

        return $this->render($template, [
            'tasks'     => $userTasks,
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
    final public function showDetails(Request $request, int $id): Response
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $details_part = 'task/_details.html.twig';
        $template     = $request->isXmlHttpRequest()
            ? $details_part
            : 'details.html.twig';

        return $this->render($template, [
            'task'   => $task,
            'entity' => $task, // For common `details`
            'title'  => 'Task',
            'details_part' => $details_part,
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
     * @param Request      $request
     * @param FileUploader $uploader
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function createTask(Request $request, FileUploader $uploader): Response
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
            $task->setOwner($this->getUser());

            $icon  = $form['icon']->getData();
            $newName = $uploader->uploadEntityIcon(
                PathKeeper::UPLOADED_ICONS_DIR,
                $icon,
                $task->getIcon()
            );
            $task->setIcon($newName);

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
     * @param Request      $request
     * @param FileUploader $uploader
     * @param integer      $id,
     *
     * @return Response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function updateTask(Request $request, FileUploader $uploader, int $id): Response
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
            $icon = $form['icon']->getData();

            if($icon) {
                $newName = $uploader->uploadEntityIcon(
                    PathKeeper::UPLOADED_ICONS_DIR,
                    $icon,
                    $task->getIcon()
                );
                $task->setIcon($newName);
            }

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
     * @param FileUploader $uploader
     * @param integer      $id
     *
     * @return Response
     * @throws FileNotFoundException
     */
    final public function deleteTask(FileUploader $uploader, int $id): Response
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $iconName = $task->getIcon();
        $uploader->deleteAvatar($iconName);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('task_list_all');
    }
}
