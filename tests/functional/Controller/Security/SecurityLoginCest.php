<?php

namespace App\Tests\Functional\Controller\Security;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Home;

/**
 * Class SecurityLoginCest
 * @package App\Tests\Functional\Controller\Security
 */
class SecurityLoginCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testSuccessfulLoginLogout(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'new_email@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'new_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('perform login action');
        $I->click($vars['guest_trigger']);
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['login_url']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'new_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->amGoingTo('check if the Login was successful');
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);

        $I->am('User');
        $I->amGoingTo('perform logout action');
        $I->click($vars['logout_link']);
        $I->seeCurrentUrlEquals($vars['url']);
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testLoginWithWrongEmail(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('email not exists in the Database');
        $I->dontSeeInRepository(User::class, ['email' => 'wrong_email@mail.ru']);

        $I->am('Dummy');
        $I->amGoingTo('perform login action with wrong email');
        $I->click($vars['guest_trigger']);
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['login_url']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'wrong_email@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->amGoingTo('check if the Login was failed');
        $I->seeCurrentUrlEquals($vars['login_url']);
        $I->see($vars['wrong_email_msg']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testLoginWithWrongPassword(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'correct_email@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'correct_email@mail.ru']);

        $I->am('Dummy');
        $I->amGoingTo('perform login action with wrong password');
        $I->click($vars['guest_trigger']);
        $I->click($vars['login_link']);
        $I->seeCurrentUrlEquals($vars['login_url']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'correct_email@mail.ru');
        $I->fillField($vars['password_field'], 'wrong_password');
        $I->click($vars['submit_button'], $vars['submit_context']);

        $I->amGoingTo('check if the Login was failed');
        $I->seeCurrentUrlEquals($vars['login_url']);
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
            'submit_button'  => SecuritySwitcher::$loginForm['submit_button_text'],
            'submit_context' => SecuritySwitcher::$loginForm['submit_button_tag'],
        ];
    }
}
