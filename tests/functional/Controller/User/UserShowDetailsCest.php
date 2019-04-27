<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\User;
use App\Tests\FunctionalTester;
use App\Tests\Page\Userlist;

/**
 * Class UserShowDetailsCest
 * @package App\Tests\Functional\Controller\User
 */
class UserShowDetailsCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testShowUserDetails(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'test_user@mail.ru',
            'avatar'   => 'anonymous.png',
            'password' => 'bla-bla-bla',
            'roles'    => ['ROLE_TEST']
        ]);
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'test_user@mail.ru']);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('find saved users in the Userlist and see it details');
        $I->see($dbUser->getId());
        $I->see($dbUser->getEmail());
        $I->see(implode(', ', $dbUser->getRoles()));

        $I->click("a[href=\"/en/user/".$user->getId()."/details\"]");
        $I->seeNumberOfElements( 'tbody tr', 1);
        $I->see($dbUser->getEmail(), 'h4');
        $I->see($dbUser->getEmail(), 'td');
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
        ];
    }
}
