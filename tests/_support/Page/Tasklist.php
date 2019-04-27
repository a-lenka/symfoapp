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
}
