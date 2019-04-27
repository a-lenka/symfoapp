<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Userlist;

/**
 * Class UserDeleteCest
 * @package App\Tests\Functional\Controller\User
 */
class UserDeleteCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testDeleteUser(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'bad_user@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Delete` link');
        $I->see('bad_user@mail.ru');
        $I->click("a[href=\"/en/user/".$user->getId()."/delete/confirm\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/user/".$user->getId()."/delete/confirm");
        $I->click($vars['delete_button']);

        $I->amGoingTo('see new email in the Userlist');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals($vars['url']);
        $I->dontSee('bad_user@mail.ru');
        $I->dontSeeInRepository(User::class, ['email' => 'bad_user@mail.ru']);
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url' => Userlist::$url['en'],
            'delete_button' => Userlist::$delete['delete_permanently_button'],
        ];
    }
}
