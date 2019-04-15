<?php

namespace App\Tests\Page\Components;

/**
 * Class SecuritySwitcher
 * @package App\Tests\Page\Components
 */
class SecuritySwitcher
{
    /** @var array $trigger - Dropdown trigger for Security actions */
    public static $trigger = [
        'context'    => 'a.dropdown-trigger',
        'guest_text' => 'person_outline',
        'user_text'  => 'person',
    ];

    /** @var array $container - Security links wrapper */
    public static $container = [
        'id' => '#dropdown-security',
    ];

    public static $form = [
        'submit_button' => 'button[type="submit"]',
    ];

    /** @var array $loginForm - Login form data */
    public static $loginForm = [
        'email_field_text'    => 'Email',
        'wrong_email_message' => 'Email could not be found',
        'password_field_text' => 'Password',
        'wrong_pswd_message'  => 'Invalid credentials',
        'remember_me_cb_text' => 'Remember me',
        'submit_button_text'  => 'Login',
        'submit_button_tag'   => 'button'
    ];



    /** @var array $login - Login data */
    public static $login = [
        'url' => '/en/login',
        'list_item_text' => 'Login'
    ];

    /** @var array $login - Logout data */
    public static $logout = [
        'url' => '/en/logout',
        'list_item_text' => 'Logout',
    ];
}
