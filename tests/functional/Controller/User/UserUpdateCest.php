<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Userlist;

/**
 * Class UserUpdateCest
 * @package App\Tests\Functional\Controller\User
 */
class UserUpdateCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testUpdateUserEmail(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'old_email@mail.ru',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Update` link');
        $I->see('old_email@mail.ru');
        $I->click("a[href=\"/en/user/".$user->getId()."/update\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/user/".$user->getId()."/update");

        $I->amGoingTo('update User email');
        $I->fillField($vars['email_field_text'], 'new_email@mail.ru');
        $I->fillField($vars['pswd_field_text'], 'kitten');
        $I->selectOption('select', 'Admin');
        $I->click($vars['submit_button']);

        $I->amGoingTo('see new email in the Userlist');
        $I->dontSee('old_email@mail.ru');
        $I->see('new_email@mail.ru');
    }


    /**
     * @param FunctionalTester $I
     */
    public function testUpdateUserRoles(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'user_email@mail.ru',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Update` link');
        $I->see('user_email@mail.ru');
        $I->click("a[href=\"/en/user/".$user->getId()."/update\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/user/".$user->getId()."/update");

        $I->amGoingTo('update User email');
        $I->fillField($vars['email_field_text'], 'user_email@mail.ru');
        $I->fillField($vars['pswd_field_text'], 'kitten');
        $I->selectOption('select', 'Admin');
        $I->click($vars['submit_button']);

        $I->amGoingTo('grab User from the database and check his roles');
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'user_email@mail.ru']);
        $I->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $dbUser->getRoles());

        $I->amGoingTo('see new email in the Userlist');
        $I->see('user_email@mail.ru');
        $I->dontSee('ROLE_TEST');
        $I->see('ROLE_ADMIN, ROLE_USER');
    }


    /**
     * @param FunctionalTester $I
     */
    public function testUpdateUserPassword(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'user_email@mail.ru',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Update` link');
        $I->see('user_email@mail.ru');
        $I->click("a[href=\"/en/user/".$user->getId()."/update\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/user/".$user->getId()."/update");

        $I->amGoingTo('update User email');
        $I->fillField($vars['email_field_text'], 'user_email@mail.ru');
        $I->fillField($vars['pswd_field_text'], 'kitten');
        $I->click($vars['submit_button']);

        $I->amGoingTo('login with new password');
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['login_url']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['login_email_field'], 'user_email@mail.ru');
        $I->fillField($vars['login_password_field'], 'kitten');
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
