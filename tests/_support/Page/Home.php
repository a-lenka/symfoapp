<?php

namespace App\Tests\Page;

/**
 * Class Home
 * @package App\Tests\Page
 */
class Home
{
    /** @var array $url - Home page URL */
    public static $url = [
        'en' => '/',
        'ru' => '/ru',
    ];

    /** @var array $header - Home Page header */
    public static $header = [
        'text'    => 'Home page',
        'tag'     => 'h1',
        'ru_text' => 'Домашняя страница',
    ];
}
