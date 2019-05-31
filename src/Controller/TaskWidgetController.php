<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TemplateRenderer;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Manage Task Widgets
 *
 * Class TaskController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class TaskWidgetController extends AbstractController
{
    /** @var TemplateRenderer */
    private $renderer;

    /**
     * AccountController constructor
     *
     * @param TemplateRenderer  $templateRenderer
     */
    public function __construct(TemplateRenderer $templateRenderer)
    {
        $this->renderer = $templateRenderer;
    }


    /**
     * @param string $property
     * @param string $value
     *
     * @return array
     */
    private function getTasksByProperty(string $property, string $value): array
    {
        $Property  = strtoupper($property);
        $method    = "get$Property";
        $tasks     = $this->getUser()->getTasks()->toArray();
        $filtered  = [];

        foreach($tasks as $task) {
            if($task->$method() === $value) {
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function renderDoneTasksPieChart(): Response
    {
        $user = $this->getUser();

        $accessMessage = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMessage, 403); }

        $tasks     = $this->getUser()->getTasks()->toArray();
        $part      = 100 / count($tasks);
        $doneTasks = $this->getTasksByProperty('state', 'Done');
        $inProgressTasks = $this->getTasksByProperty('state', 'In progress');

        $props = [
            'page'  => 'widgets/_done_tasks_pie.html.twig',
            'tasks' => $tasks,
            'part'  => $part,
            'done_tasks'      => $doneTasks,
            'done_tasks_part' => count($doneTasks) * $part,
            'in_progress_tasks'      => $inProgressTasks,
            'in_progress_tasks_part' => count($inProgressTasks) * $part,
        ];

        return new Response(
            $this->renderer->renderTemplate($props)
        );
    }


    /**
     * @return Response
     * @throws Exception
     */
    final public function renderOverdueTasksDonutChart(): Response
    {
        $user = $this->getUser();

        $accessMessage = 'Login please. You can access this page only from your account';
        if(!$user) { throw new AccessDeniedException($accessMessage, 403); }

        $tasks = $this->getUser()->getTasks()->toArray();
        $part  = 100 / count($tasks);
        $overdueTasks    = $this->getOverdueTasks();
        $overdueTasksPart= count($overdueTasks) * $part;
        $inProgressTasks = $this->getTasksByProperty('state', 'In progress');
        $inProgressTasksPart = count($inProgressTasks) * $part;

        $props = [
                'page'        => 'widgets/_overdue_tasks_donut.html.twig',
                'tasks'       => $tasks,
                'part'        => $part,
                'overdue_tasks'     => $overdueTasks,
                'overdue_tasks_part'=> $overdueTasksPart,
                'in_progress_tasks' => $inProgressTasks,
                'in_progress_tasks_part' => $inProgressTasksPart,
        ];

        return new Response(
            $this->renderer->renderTemplate($props)
        );
    }
}
