<?php

namespace App\Tests\Page\Components;

/**
 * Class LocaleSwitcher
 * @package App\Tests\Page\Components
 */
class LocaleSwitcher
{
    /** @var array $trigger - Dropdown trigger for list of locales */
    public static $trigger = [
        'text' => 'language',
    ];

    /** @var array $links - Locale links */
    public static $links = [
        'ru_text' => 'Русский',
        'en_text' => 'English',
    ];
}
