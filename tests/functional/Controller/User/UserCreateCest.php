<?php

namespace App\Tests\Functional\Controller\User;

use App\Tests\FunctionalTester;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Userlist;

/**
 * Class UserCreateCest
 * @package App\Tests\Functional\Controller\User
 */
class UserCreateCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testCreateUser(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->am('Tester');
        $I->amOnPage($vars['url']);

        $I->amGoingTo('click `Create` link');
        $I->click($vars['create_button']);
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals($vars['create_url']);

        $I->amGoingTo('create new User');
        $I->fillField($vars['email_field_text'], 'new_user@mail.ru');
        $I->fillField($vars['pswd_field_text'], 'new_kitten');
        $I->selectOption('select', 'Admin');
        $I->click($vars['submit_button']);

        $I->amGoingTo('see new email in the Userlist');
        $I->see('new_user@mail.ru');
        $I->see('ROLE_ADMIN, ROLE_USER');

        $I->amGoingTo('login with new password');
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['login_url']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['login_email_field'], 'new_user@mail.ru');
        $I->fillField($vars['login_password_field'], 'new_kitten');
        $I->click($vars['login_submit_button']);

        $I->amGoingTo('check if the Login was successful');
        $I->seeCurrentUrlEquals('/');
        $I->dontSee($vars['guest_trigger']);
        $I->see($vars['user_trigger']);
        $I->dontSee($vars['login_wrong_email_msg']);
        $I->dontSee($vars['login_wrong_pswd_msg']);
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url'         => Userlist::$url['en'],
            'header_text' => Userlist::$header['text'],
            'header_tag'  => Userlist::$header['tag'],
            'user_link'   => Userlist::$links['topmenu_link_text'],
            'create_url'   => Userlist::$create['create_url'],
            'create_button'=> Userlist::$create['create_btn_icon_text'],
            'email_field_text' => Userlist::$form['email_field_text'],
            'roles_field_text' => Userlist::$form['roles_field_text'],
            'pswd_field_text'  => Userlist::$form['password_field_text'],
            'submit_button'    => Userlist::$form['submit_button_tag'],
            'select_tag' => Userlist::$form['select_tag'],
            'root_role'  => Userlist::$form['root_option'],
            'admin_role' => Userlist::$form['admin_option'],
            'user_role'  => Userlist::$form['user_option'],
            'guest_trigger' => SecuritySwitcher::$trigger['guest_text'],
            'user_trigger'  => SecuritySwitcher::$trigger['user_text'],
            'login_link'    => SecuritySwitcher::$login['list_item_text'],
            'login_url'     => SecuritySwitcher::$login['url'],
            'login_email_field'    => SecuritySwitcher::$loginForm['email_field_text'],
            'login_wrong_email_msg'=> SecuritySwitcher::$loginForm['wrong_email_message'],
            'login_password_field' => SecuritySwitcher::$loginForm['password_field_text'],
            'login_wrong_pswd_msg' => SecuritySwitcher::$loginForm['wrong_pswd_message'],
            'login_submit_button'  => 'button[type="submit"]',
        ];
    }
}
