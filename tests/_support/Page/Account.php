<?php

namespace App\Tests\Page;

/**
 * Class Account
 * @package App\Tests\Page
 */
class Account
{
    /** @var array $url - Account page URL */
    public static $url = [
        'en' => '/en/account',
        'ru' => '/ru/account',
    ];

    /** @var array $header - Account page header */
    public static $header = [
        'text'    => 'Account page',
        'tag'     => 'h1',
        'ru_text' => 'Страница пользователя',
    ];
}
