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
        'form_tag'      => 'form',
        'submit_button' => 'button[type="submit"]',
    ];

    /** @var array $loginForm - Login form data */
    public static $loginForm = [
        'header_text'         => 'Login',
        'header_tag'          => 'h4',
        'email_field_text'    => 'Email',
        'wrong_email_message' => 'Email could not be found',
        'password_field_text' => 'Password',
        'wrong_pswd_message'  => 'Invalid credentials',
        'remember_me_cb_text' => 'Remember me',
        'submit_button_text'  => 'Login',
        'submit_button_tag'   => 'button',
        'forbidden_error_msg' => 'We are sorry, but you do not have access to this page. Please, login',
    ];

    /** @var array $registerForm - Register form data */
    public static $registerForm = [
        'header_text'         => 'Registration',
        'header_tag'          => 'h4',
        'email_field_text'    => 'Email',
        'first_password_text' => 'Password',
        'second_password_text'=> 'Repeat password',
        'wrong_pswd_message'  => 'This value is not valid.',
        'terms_checkbox_text' => 'Remember me',
        'submit_button_tag'   => 'button[type="submit"]',
    ];

    /** @var array $resetForm - Reset password form data */
    public static $resetForm = [
        'header_text'         => 'Reset password',
        'header_tag'          => 'h4',
        'current_password_text' => 'Current password',
        'new_password_text'     => 'New password',
        'confirm_password_text' => 'Confirm new password',
        'submit_button_tag'   => 'button[type="submit"]',
        'wrong_curr_pswd_msg' => "This value should be the user's current password",
        'mismatch_pswd_msg'   => 'This value is not valid.'
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

    /** @var array $register - Register data */
    public static $register = [
        'url' => '/en/register',
        'list_item_text' => 'Register'
    ];

    public static $account = [
        'link_text' => 'Account',
    ];

    /** @var array $reset - Reset data */
    public static $reset = [
        'url' => '/en/reset',
        'list_item_text' => 'Reset password'
    ];
}
