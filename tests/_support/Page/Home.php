<?php

namespace App\Tests\Page;

/**
 * Class Home
 * @package App\Tests\Page
 */
class Home
{
    /** @var string $URL - Home page URL */
    private static $URL = '/';

    /** @var array $header - Home Page header */
    private static $header = [
        'text' => 'Home page',
        'tag'  => 'h1',
    ];


    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: `Page\Edit::route('/123-post')`;
     *
     * @param string $param
     *
     * @return string
     */
    public static function route(string $param): string
    {
        return static::$URL.$param;
    }


    /**
     * Returns page data
     * NOTE: Do not use `get_class_vars()` because PHPStorm do not hint values
     *
     * @return array
     */
    public static function getVars(): array
    {
        return [
            'url'         => self::$URL,
            'header_text' => self::$header['text'],
            'header_tag'  => self::$header['tag'],
        ];
    }
}
