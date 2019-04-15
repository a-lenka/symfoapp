<?php

namespace App\Tests\Acceptance\Pages;

use App\Tests\AcceptanceTester;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Home;
use Exception;

/**
 * Class LoginCest
 * @package App\Tests\Acceptance\Pages
 */
class LoginCest
{
    /**
     * @param AcceptanceTester $I
     *
     * @throws Exception
     *
     */
    public function testSuccessfulLoginLogout(AcceptanceTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('get needed services');
        $I->haveInDatabase('user', [
            'email'    => 'new_email@mail.ru',
            'password' => '$argon2i$v=19$m=1024,t=2,p=2$dFMuV0R2RVN0ZXJZMFRZdA$ggI9ZH8OTdumEsJxQXJTSIOSpHjzpXzj4TTlX7u5/wM',
            'roles'    => '["ROLE_TEST"]',
        ]);

        $I->seeInDatabase('user', ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('fill Login form');
        $I->click($vars['guest_trigger']);
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['url']);

        $I->amGoingTo('fill Login form');
        $I->waitForText($vars['email_field']);
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button']);

        $I->expect('Login was successful');
        $I->wait(2);
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);

        $I->am('User');
        $I->amGoingTo('perform Logout action');
        $I->click($vars['user_trigger']);
        $I->click($vars['logout_link']);

        $I->expect('I am unLogged');
        $I->seeCurrentUrlEquals($vars['url']);
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
    }


    /**
     * @param AcceptanceTester $I
     *
     * @throws Exception
     */
    public function testLoginWithWrongEmail(AcceptanceTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('get needed services');
        $I->haveInDatabase('user', [
            'email'    => 'new_email@mail.ru',
            'password' => '$argon2i$v=19$m=1024,t=2,p=2$dFMuV0R2RVN0ZXJZMFRZdA$ggI9ZH8OTdumEsJxQXJTSIOSpHjzpXzj4TTlX7u5/wM',
            'roles'    => '["ROLE_TEST"]',
        ]);

        $I->seeInDatabase('user', ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('test Login action with the wrong email');
        $I->amGoingTo('fill Login form');
        $I->click($vars['guest_trigger']);
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['url']);

        $I->amGoingTo('fill Login form');
        $I->waitForText($vars['email_field']);
        $I->fillField($vars['email_field'], 'wrong_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button']);

        $I->expect('Login was fail');
        $I->wait(2);
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['wrong_email_msg']);
    }


    /**
     * @param AcceptanceTester $I
     *
     * @throws Exception
     */
    public function testLoginWithWrongPassword(AcceptanceTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('get needed services');
        $I->haveInDatabase('user', [
            'email'    => 'new_email@mail.ru',
            'password' => '$argon2i$v=19$m=1024,t=2,p=2$dFMuV0R2RVN0ZXJZMFRZdA$ggI9ZH8OTdumEsJxQXJTSIOSpHjzpXzj4TTlX7u5/wM',
            'roles'    => '["ROLE_TEST"]',
        ]);

        $I->seeInDatabase('user', ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('test Login action with the wrong password');
        $I->amGoingTo('fill Login form');
        $I->click($vars['guest_trigger']);
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['url']);

        $I->amGoingTo('fill Login form');
        $I->waitForText($vars['email_field']);
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'wrong_password');
        $I->click($vars['submit_button']);

        $I->expect('Login was fail');
        $I->wait(2);
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['wrong_pswd_msg']);
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
            'url'            => Home::$url['en'],
            'ru_url'         => Home::$url['ru'],
            'trigger_context'=> SecuritySwitcher::$trigger['context'],
            'guest_trigger'  => SecuritySwitcher::$trigger['guest_text'],
            'user_trigger'   => SecuritySwitcher::$trigger['user_text'],
            'login_link'     => SecuritySwitcher::$login['list_item_text'],
            'login_url'      => SecuritySwitcher::$login['url'],
            'logout_url'     => SecuritySwitcher::$logout['url'],
            'logout_link'    => SecuritySwitcher::$logout['list_item_text'],
            'email_field'    => SecuritySwitcher::$loginForm['email_field_text'],
            'wrong_email_msg'=> SecuritySwitcher::$loginForm['wrong_email_message'],
            'password_field' => SecuritySwitcher::$loginForm['password_field_text'],
            'wrong_pswd_msg' => SecuritySwitcher::$loginForm['wrong_pswd_message'],
            'submit_context' => SecuritySwitcher::$loginForm['submit_button_tag'],
            'submit_button'  => SecuritySwitcher::$form['submit_button'],
        ];
    }
}
