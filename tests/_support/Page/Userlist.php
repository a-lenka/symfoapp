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

    /** @var array $form - Form data */
    public static $form = [
        'email_field_text'    => 'User email',
        'roles_field_text'    => 'User roles',
        'password_field_text' => 'User password',
        'submit_button_tag'   => 'button',
        'admin_option'        => 'Admin',
        'root_option'         => 'Root',
        'user_option'         => 'User',
        'select_tag'          => 'select',
    ];

    public static $create = [
        'create_url' => '/en/user/create',
        'create_btn_icon_text'=> 'add',
    ];
}
