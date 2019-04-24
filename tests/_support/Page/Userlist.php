<?php

namespace App\Tests\Page;

/**
 * Class Userlist
 * @package App\Tests\Page
 */
class Userlist
{
    /** @var array $url - Userlist page URL */
    public static $url = [
        'en' => '/en/user/list/all',
        'ru' => '/ru/user/list/all',
    ];


    /** @var array $header - Userlist page header */
    public static $header = [
        'text' => 'Users',
        'tag'  => 'h1',
    ];


    /** @var array $links - Links to Userlist page */
    public static $links = [
        'topmenu_link_text' => 'Users'
    ];
}
