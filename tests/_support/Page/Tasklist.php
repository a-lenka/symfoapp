<?php

namespace App\Tests\Page;

/**
 * Class Tasklist
 * @package App\Tests\Page
 */
class Tasklist
{
    /** @var array $url - Tasklist page URL */
    public static $url = [
        'en' => '/en/task/list/all',
        'ru' => '/ru/task/list/all',
        'create' => '/en/task/create'
    ];


    /** @var array $header - Tasklist page header */
    public static $header = [
        'text' => 'Tasks',
        'tag'  => 'h1',
    ];


    /** @var array $links - Links to Tasklist page */
    public static $links = [
        'topmenu_link_text' => 'Tasks'
    ];


    /** @var array $sortLinks - Links to sort Tasks */
    public static $sortLinks = [
        'title_asc'  => '/en/task/list/all/sorted/title/asc',
        'title_desc' => '/en/task/list/all/sorted/title/desc',
        'deadline_asc'  => '/en/task/list/all/sorted/dateDeadline/asc',
        'deadline_desc' => '/en/task/list/all/sorted/dateDeadline/desc',
        'state_asc'  => '/en/task/list/all/sorted/state/asc',
        'state_desc' => '/en/task/list/all/sorted/state/desc',
    ];


    /** @var array $form - Form data */
    public static $form = [
        'title_field_text' => 'Task name',
        'deadline_date_field_text' => 'Date',
        'deadline_time_field_text' => 'Time',
        'submit_button' => 'button[type="submit"]',
        'in_progress'   => 'In progress',
        'done'          => 'Done',
    ];


    /** @var array $create */
    public static $create = [
        'create_btn_icon_text'=> 'add',
    ];


    /** @var array $delete */
    public static $delete = [
        'delete_permanently_button'=> 'Delete permanently',
    ];
}
