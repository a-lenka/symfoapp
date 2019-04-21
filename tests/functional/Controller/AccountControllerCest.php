<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Account;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Home;

/**
 * Class AccountControllerCest
 * @package App\Tests\Functional\Controller
 */
class AccountControllerCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testAnonymousClickAccountFromHomePage(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['account_url']);

        $I->am('Guest');
        $I->amGoingTo('see Account page content');
        $I->seeResponseCodeIsSuccessful();

        $I->expect('I am on the Login page and see no errors');
        $I->seeCurrentUrlEquals($vars['login_url']);
        $I->dontSee($vars['forbidden_msg']);
        $I->dontSee($vars['account_link']);
        $I->see($vars['login_link_text']);
        $I->see($vars['login_form_header_text'], $vars['login_form_header_tag']);
        $I->seeElement($vars['login_form_tag']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testUserClickAccountFromHomePage(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User with `ROLE_USER` to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'new_email@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_USER']
        ]);
        $I->seeInRepository(User::class, ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->expect('I am on Home page');
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['home_header_text'], $vars['home_header_tag']);

        $I->amGoingTo('submit Login form with valid credentials');
        $I->click($vars['login_link_text']);
        $I->seeCurrentUrlEquals($vars['login_url']);
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->expect('login was successful and I was redirected to Home page');
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['user_trigger'], $vars['trigger_context']);
        $I->see($vars['home_header_text'], $vars['home_header_tag']);

        $I->am('User');
        $I->amGoingTo('see Account page');
        $I->click($vars['account_link']);
        $I->seeCurrentUrlEquals($vars['account_url']);
        $I->see($vars['forbidden_msg']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testAdminClickAccountFromHomePage(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User with `ROLE_ADMIN` to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'new_email@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_ADMIN']
        ]);
        $I->seeInRepository(User::class, ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->expect('I am on Home page');
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['home_header_text'], $vars['home_header_tag']);

        $I->amGoingTo('submit Login form with valid credentials');
        $I->click($vars['login_link_text']);
        $I->seeCurrentUrlEquals($vars['login_url']);
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->expect('login was successful and I was redirected to Home page');
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['user_trigger'], $vars['trigger_context']);
        $I->see($vars['home_header_text'], $vars['home_header_tag']);

        $I->am('Admin');
        $I->amGoingTo('see Account page');
        $I->click($vars['account_link']);
        $I->seeCurrentUrlEquals($vars['account_url']);
        $I->see($vars['forbidden_msg']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testRootClickAccountFromHomePage(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User with `ROLE_ROOT` to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'new_email@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_ROOT']
        ]);
        $I->seeInRepository(User::class, ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->expect('I am on Home page');
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['home_header_text'], $vars['home_header_tag']);

        $I->amGoingTo('submit Login form with valid credentials');
        $I->click($vars['login_link_text']);
        $I->seeCurrentUrlEquals($vars['login_url']);
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->expect('login was successful and I was redirected to Home page');
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['user_trigger'], $vars['trigger_context']);
        $I->see($vars['home_header_text'], $vars['home_header_tag']);

        $I->am('Root');
        $I->amGoingTo('see Account page');
        $I->click($vars['account_link']);
        $I->seeCurrentUrlEquals($vars['account_url']);
        $I->dontSee($vars['forbidden_msg']);
        $I->see($vars['account_header_text'], $vars['account_header_tag']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testRedirectToTargetUrl(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['account_url']);

        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User with `ROLE_ROOT` to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'new_email@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_ROOT']
        ]);
        $I->seeInRepository(User::class, ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->expect('I was redirected to Login page');
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
        $I->seeCurrentUrlEquals($vars['login_url']);

        $I->amGoingTo('submit Login form');
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->expect('login was successful');
        $I->dontSee($vars['forbidden_msg']);
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['user_trigger'], $vars['trigger_context']);

        $I->am('Root');
        $I->expect('I was redirected to Account page');
        $I->seeCurrentUrlEquals($vars['account_url']);
        $I->see($vars['account_header_text'], $vars['account_header_tag']);
    }


    /**
     * NOTE: 1. I have not used `get_class_vars()` because PHPStorm do not hint it values.
     *       2. `getVars()` is here, because if I do not want to wrestle
     *              with how to paste in tests an additional portion of data
     *              saving some readability.
     *       3. All the possible changes are only here.
     *       4. Symfony translator is cheerful guy :)
     *
     * @return array
     */
    private static function getVars(): array
    {
        return [
            // Pages
            'home_url'        => Home::$url['en'],
            'home_header_text'=> Home::$header['text'],
            'home_header_tag' => Home::$header['tag'],
            'account_url'    => Account::$url['en'],
            'account_link'   => SecuritySwitcher::$account['link_text'],
            'account_header_text' => Account::$header['text'],
            'account_header_tag'  => Account::$header['tag'],
            // Security Trigger
            'guest_trigger'  => SecuritySwitcher::$trigger['guest_text'],
            'user_trigger'   => SecuritySwitcher::$trigger['user_text'],
            'trigger_context'=> SecuritySwitcher::$trigger['context'],
            // Login Form
            'login_url'      => SecuritySwitcher::$login['url'],
            'login_link_text'=> SecuritySwitcher::$login['list_item_text'],
            'login_form_header_text' => SecuritySwitcher::$loginForm['header_text'],
            'login_form_header_tag'  => SecuritySwitcher::$loginForm['header_tag'],
            'login_form_tag' => SecuritySwitcher::$form['form_tag'],
            'email_field'    => SecuritySwitcher::$loginForm['email_field_text'],
            'password_field' => SecuritySwitcher::$loginForm['password_field_text'],
            'submit_button'  => SecuritySwitcher::$loginForm['submit_button_text'],
            'submit_context' => SecuritySwitcher::$loginForm['submit_button_tag'],
            'forbidden_msg'  => SecuritySwitcher::$loginForm['forbidden_error_msg'],
        ];
    }
}
