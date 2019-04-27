<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Home;
use App\Tests\Page\Userlist;

/**
 * Class UserShowListCest
 * @package App\Tests\Functional\Controller\User
 */
class UserShowListCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testShowAllUsers(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('save new Users to the Database');
        $first = new User();
        $I->persistEntity($first, [
            'email'    => 'first_user@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'first_user@mail.ru']);


        $second = new User();
        $I->persistEntity($second, [
            'email'    => 'second_user@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'second_user@mail.ru']);


        $third = new User();
        $I->persistEntity($third, [
            'email'    => 'third_user@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'third_user@mail.ru']);


        $I->am('Tester');
        $I->amGoingTo('see all saved users in the Userlist');
        $I->click($vars['user_link']);
        $I->seeCurrentUrlEquals($vars['userlist_url']);
        $I->see('first_user@mail.ru');
        $I->see('second_user@mail.ru');
        $I->see('third_user@mail.ru');
    }


    /**
     * @param FunctionalTester $I
     */
    public function testSortUsersByEmail(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('save new Users to the Database');
        $first = new User();
        $I->persistEntity($first, [
            'email'    => 'aaa@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'aaa@mail.ru']);


        $third = new User();
        $I->persistEntity($third, [
            'email'    => 'zzz@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'zzz@mail.ru']);


        $I->am('Tester');
        $I->amGoingTo('see all saved users in the Userlist');
        $I->click($vars['user_link']);
        $I->seeCurrentUrlEquals($vars['userlist_url']);
        $I->see('aaa@mail.ru');
        $I->see('zzz@mail.ru');

        $I->amGoingTo('sort Users with desc order');
        $I->click('a[href="'.$vars['email_desc'].'"]');
        $I->see('zzz@mail.ru', 'table tbody tr:first-child');

        $I->amGoingTo('sort Users with asc order');
        $I->click('a[href="'.$vars['email_asc'].'"]');
        $I->see('aaa@mail.ru', 'table tbody tr:first-child');
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'home_url'    => Home::$url['en'],
            'userlist_url'=> Userlist::$url['en'],
            'header_text' => Userlist::$header['text'],
            'header_tag'  => Userlist::$header['tag'],
            'user_link'   => Userlist::$links['topmenu_link_text'],
            'email_asc'   => Userlist::$sort['email_asc'],
            'email_desc'  => Userlist::$sort['email_desc'],
            'roles_asc'   => Userlist::$sort['roles_asc'],
            'roles_desc'  => Userlist::$sort['roles_desc'],
        ];
    }
}
