<?php

namespace App\Tests\Functional\Controller\Security;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Home;

/**
 * Class SecurityResetCest
 * @package App\Tests\Functional\Controller\Security
 */
class SecurityResetCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testSuccessfulResetPassword(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'reset_password@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_USER']
        ]);
        $I->seeInRepository(User::class, ['email' => 'reset_password@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('click login link');
        $I->click($vars['login_link']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'reset_password@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_login_button']);

        $I->amGoingTo('check if the Login was successful');
        $I->dontSee($vars['guest_trigger']);
        $I->dontSee($vars['wrong_login_email']);
        $I->dontSee($vars['wrong_login_pswd']);

        $I->am('User');
        $I->amGoingTo('click reset password link');
        $I->click($vars['reset_link']);
        $I->seeCurrentUrlEquals($vars['reset_url']);

        $I->amGoingTo('reset password finally');
        $I->fillField($vars['current_pswd_field'], 'kitten');
        $I->fillField($vars['new_pswd_field'], 'new_password');
        $I->fillField($vars['confirm_pswd_field'], 'new_password');
        $I->click($vars['submit_button']);

        $I->amGoingTo('check if the Reset password was successful');
        $I->seeResponseCodeIsSuccessful();
        $I->dontSee($vars['wrong_curr_pswd_msg']);
        $I->dontSee($vars['mismatch_pswd_msg']);
        $I->see($vars['guest_trigger'], $vars['trigger_context']);

        $I->am('Guest');
        $I->amGoingTo('login with new password');
        $I->click($vars['login_link']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'reset_password@mail.ru');
        $I->fillField($vars['password_field'], 'new_password');
        $I->click($vars['submit_login_button']);

        $I->amGoingTo('check if the Login was successful');
        $I->dontSee($vars['guest_trigger']);
        $I->dontSee($vars['wrong_login_email']);
        $I->dontSee($vars['wrong_login_pswd']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testResetPasswordWithWrongCurrentPassword(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'reset_password@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_USER']
        ]);
        $I->seeInRepository(User::class, ['email' => 'reset_password@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('click login link');
        $I->click($vars['login_link']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'reset_password@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_login_button']);

        $I->amGoingTo('check if the Login was successful');
        $I->dontSee($vars['guest_trigger']);
        $I->dontSee($vars['wrong_login_email']);
        $I->dontSee($vars['wrong_login_pswd']);

        $I->am('User');
        $I->amGoingTo('click reset password link');
        $I->click($vars['reset_link']);
        $I->seeCurrentUrlEquals($vars['reset_url']);

        $I->amGoingTo('reset password with invalid current password');
        $I->fillField($vars['current_pswd_field'], 'wrong_password');
        $I->fillField($vars['new_pswd_field'], 'new_password');
        $I->fillField($vars['confirm_pswd_field'], 'new_password');
        $I->click($vars['submit_button']);

        $I->amGoingTo('check if the Reset password was failed');
        $I->seeResponseCodeIsSuccessful();
        $I->see($vars['wrong_curr_pswd_msg']);
        $I->dontSee($vars['mismatch_pswd_msg']);
        $I->seeCurrentUrlEquals($vars['reset_url']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testResetPasswordWithMismatchNewPasswords(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'reset_password@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_USER']
        ]);
        $I->seeInRepository(User::class, ['email' => 'reset_password@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('click login link');
        $I->click($vars['login_link']);

        $I->amGoingTo('fill Login form');
        $I->fillField($vars['email_field'], 'reset_password@mail.ru');
        $I->fillField($vars['password_field'], 'kitten');
        $I->click($vars['submit_login_button']);

        $I->amGoingTo('check if the Login was successful');
        $I->dontSee($vars['guest_trigger']);
        $I->dontSee($vars['wrong_login_email']);
        $I->dontSee($vars['wrong_login_pswd']);

        $I->am('User');
        $I->amGoingTo('click reset password link');
        $I->click($vars['reset_link']);
        $I->seeCurrentUrlEquals($vars['reset_url']);

        $I->amGoingTo('reset password with mismatches new passwords');
        $I->fillField($vars['current_pswd_field'], 'kitten');
        $I->fillField($vars['new_pswd_field'], 'new_password');
        $I->fillField($vars['confirm_pswd_field'], 'another_password');
        $I->click($vars['submit_button']);

        $I->amGoingTo('check if the Reset password was failed');
        $I->seeResponseCodeIsSuccessful();
        $I->dontSee($vars['wrong_curr_pswd_msg']);
        $I->see($vars['mismatch_pswd_msg']);
        $I->seeCurrentUrlEquals($vars['reset_url']);
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
            'login_url'      => SecuritySwitcher::$login['url'],
            'login_link'     => SecuritySwitcher::$login['list_item_text'],
            'reset_url'      => SecuritySwitcher::$reset['url'],
            'reset_link'     => SecuritySwitcher::$reset['list_item_text'],
            'current_pswd_field' => SecuritySwitcher::$resetForm['current_password_text'],
            'new_pswd_field'     => SecuritySwitcher::$resetForm['new_password_text'],
            'confirm_pswd_field' => SecuritySwitcher::$resetForm['confirm_password_text'],
            'wrong_curr_pswd_msg'=> SecuritySwitcher::$resetForm['wrong_curr_pswd_msg'],
            'mismatch_pswd_msg'  => SecuritySwitcher::$resetForm['mismatch_pswd_msg'],
            'submit_button'      => SecuritySwitcher::$resetForm['submit_button_tag'],
            'wrong_login_email' => SecuritySwitcher::$loginForm['wrong_email_message'],
            'wrong_login_pswd'  => SecuritySwitcher::$loginForm['wrong_pswd_message'],
            'email_field'    => SecuritySwitcher::$loginForm['email_field_text'],
            'wrong_email_msg'=> SecuritySwitcher::$loginForm['wrong_email_message'],
            'password_field' => SecuritySwitcher::$loginForm['password_field_text'],
            'wrong_pswd_msg' => SecuritySwitcher::$loginForm['wrong_pswd_message'],
            'submit_login_button' => SecuritySwitcher::$loginForm['submit_button_tag'],
        ];
    }
}
