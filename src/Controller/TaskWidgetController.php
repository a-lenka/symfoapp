<?php

namespace App\Controller;

use App\Entity\Task;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manage Task Widgets
 *
 * Class TaskController
 * @package App\Controller
 */
class TaskWidgetController extends AbstractController
{
    /**
     * @param string $property
     * @param string $value
     *
     * @return array
     */
    private function getTasksByProperty(string $property, string $value): array
    {
        $Property  = strtoupper($property);
        $getProp   = "get$Property";
        $tasks     = $this->getUser()->getTasks()->toArray();
        $filtered  = [];

        foreach($tasks as $task) {
            if($task->$getProp() === $value) {
                $filtered[] = $task;
            }
        }

        return $filtered;
    }


    /**
     * @return array
     * @throws Exception
     */
    private function getOverdueTasks(): array
    {
        /** @var Task[] $tasks */
        $tasks    = $this->getUser()->getTasks()->toArray();
        $filtered = [];

        $now = new DateTime('now');

        foreach($tasks as $task) {
            if($task->getDateDeadline() < $now) {
                $filtered[] = $task;
            }
        }

        return $filtered;
    }


    /**
     * @return Response
     */
    final public function renderDoneTasksPieChart(): Response
    {
        $user = $this->getUser();

        if(!$user) {
            throw new AccessDeniedException(
            /** TODO: Secure the link from non authenticated Users instead */
                'Login please. You can access this page only from your account.', 403
            );
        }

        $tasks      = $this->getUser()->getTasks()->toArray();
        $part       = 100 / count($tasks);
        $doneTasks  = $this->getTasksByProperty('state', 'Done');
        $doneTasksPart   = count($doneTasks) * $part;
        $inProgressTasks = $this->getTasksByProperty('state', 'In progress');
        $inProgressTasksPart  = count($inProgressTasks) * $part;

        return $this->render(
            'widgets/_done_tasks_pie.html.twig', [
                'tasks'       => $tasks,
                'part'        => $part,
                'done_tasks'  => $doneTasks,
                'done_tasks_part'   => $doneTasksPart,
                'in_progress_tasks' => $inProgressTasks,
                'in_progress_tasks_part'  => $inProgressTasksPart,
            ]
        );
    }


    /**
     * @return Response
     * @throws Exception
     */
    final public function renderOverdueTasksDonutChart(): Response
    {
        $user = $this->getUser();

        if(!$user) {
            throw new AccessDeniedException(
            /** TODO: Secure the link from non authenticated Users instead */
                'Login please. You can access this page only from your account.', 403
            );
        }

        $tasks = $this->getUser()->getTasks()->toArray();
        $part  = 100 / count($tasks);
        $overdueTasks    = $this->getOverdueTasks();
        $overdueTasksPart= count($overdueTasks) * $part;
        $inProgressTasks = $this->getTasksByProperty('state', 'In progress');
        $inProgressTasksPart = count($inProgressTasks) * $part;

        return $this->render(
            'widgets/_overdue_tasks_donut.html.twig', [
                'tasks'       => $tasks,
                'part'        => $part,
                'overdue_tasks'     => $overdueTasks,
                'overdue_tasks_part'=> $overdueTasksPart,
                'in_progress_tasks' => $inProgressTasks,
                'in_progress_tasks_part'  => $inProgressTasksPart,
            ]
        );
    }
}