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


    /** @var array $sort - Links to sort Users */
    public static $sort = [
        'email_asc'  => '/en/user/list/all/sorted/email/asc',
        'email_desc' => '/en/user/list/all/sorted/email/desc',
        'roles_asc'  => '/en/user/list/all/sorted/roles/asc',
        'roles_desc' => '/en/user/list/all/sorted/roles/desc',
    ];

    /** @var array $form - Form data */
    public static $form = [
        'email_field_text'    => 'User email',
        'file_field_tag'      => 'input[type="file"]',
        'roles_field_text'    => 'User roles',
        'password_field_text' => 'User password',
        'submit_button_tag'   => 'button[type="submit"]',
        'admin_option'        => 'Admin',
        'root_option'         => 'Root',
        'user_option'         => 'User',
        'select_tag'          => 'select',
        'empty_file_msg'      => 'An empty file is not allowed.',
        'no_image_msg'        => 'This file is not a valid image.',
    ];

    /** @var array $create */
    public static $create = [
        'create_url' => '/en/user/create',
        'create_btn_icon_text'=> 'add',
    ];

    /** @var array $delete */
    public static $delete = [
        'delete_permanently_button'=> 'Delete permanently',
    ];
}
