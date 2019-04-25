<?php

namespace App\Tests\Functional\Controller\Security;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Components\SecuritySwitcher;
use App\Tests\Page\Home;

/**
 * Class SecurityRegisterCest
 * @package App\Tests\Functional\Controller\Security
 */
class SecurityRegisterCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testSuccessfulRegistration(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->dontSeeInRepository(User::class, ['email' => 'registration_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('perform registration action');
        $I->click($vars['guest_trigger']);
        $I->click($vars['register_link']);
        $I->seeCurrentUrlEquals($vars['register_url']);

        $I->amGoingTo('fill Register form');
        $I->fillField($vars['email_field'], 'registration_email@mail.ru');
        $I->fillField($vars['first_pswd_field'], 'kitten');
        $I->fillField($vars['second_pswd_field'], 'kitten');
        $I->checkOption('Terms accepted');
        $I->click($vars['submit_button']);

        $I->amGoingTo('check if the Login was successful');
        $I->dontSee($vars['wrong_pswd_msg']);
        $I->seeResponseCodeIsSuccessful();
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testRegisterWithMismatchesPasswords(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->dontSeeInRepository(User::class, ['email' => 'registration_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('perform registration action');
        $I->click($vars['guest_trigger']);
        $I->click($vars['register_link']);
        $I->seeCurrentUrlEquals($vars['register_url']);

        $I->amGoingTo('fill Register form');
        $I->fillField($vars['email_field'], 'registration_email@mail.ru');
        $I->fillField($vars['first_pswd_field'], 'kitten');
        $I->fillField($vars['second_pswd_field'], 'wrong');
        $I->checkOption('Terms accepted');
        $I->click($vars['submit_button']);

        $I->amGoingTo('check if the Register was failed');
        $I->seeCurrentUrlEquals($vars['register_url']);
        $I->see($vars['wrong_pswd_msg']);
        $I->see($vars['guest_trigger'], $vars['trigger_context']);
    }


    /**
     * The EntityManager is closed.
     * @param FunctionalTester $I

    public function testRegisterWithExistingEmail(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amOnPage($vars['url']);

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'existing_email@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_USER']
        ]);
        $I->seeInRepository(User::class, ['email' => 'existing_email@mail.ru']);

        $I->am('Guest');
        $I->amGoingTo('perform registration action');
        $I->click($vars['guest_trigger']);
        $I->click($vars['register_link']);
        $I->seeCurrentUrlEquals($vars['register_url']);

        $I->amGoingTo('fill Register form');
        $I->fillField($vars['email_field'], 'existing_email@mail.ru');
        $I->fillField($vars['first_pswd_field'], 'kitten');
        $I->fillField($vars['second_pswd_field'], 'kitten');
        $I->checkOption('Terms accepted');
        $I->click($vars['submit_button']);

        $I->amGoingTo('check if the Register was failed');
        $I->seeCurrentUrlEquals($vars['register_url']);
        $I->dontSee($vars['guest_trigger'], $vars['trigger_context']);
        $I->see($vars['sql_exception']);
    }*/


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
            'register_link'  => SecuritySwitcher::$register['list_item_text'],
            'register_url'   => SecuritySwitcher::$register['url'],
            'email_field'    => SecuritySwitcher::$registerForm['email_field_text'],
            'first_pswd_field'  => SecuritySwitcher::$registerForm['first_password_text'],
            'second_pswd_field' => SecuritySwitcher::$registerForm['second_password_text'],
            'wrong_pswd_msg' => SecuritySwitcher::$registerForm['wrong_pswd_message'],
            'submit_button'  => SecuritySwitcher::$registerForm['submit_button_tag'],
            'sql_exception'  => 'Integrity constraint violation'
        ];
    }
}
