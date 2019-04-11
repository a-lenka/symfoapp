<?php

namespace App\Tests;

use App\Entity\User;

/**
 * Class UserEntityCest
 * @package App\Tests
 */
class UserEntityCest
{
    /**
     * @param UnitTester $I
     */
    public function validUserWasSavedSuccessfully(UnitTester $I): void
    {
        $I->amGoingTo('get needed services');
        $encoder = $I->grabService('security.password_encoder');

        $I->amGoingTo('save new valid User to the Database');
        $user = new User();
        $I->persistEntity($user, [
            'email'    => 'new_email@mail.ru',
            'password' => $encoder->encodePassword($user, 'kitten'),
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'new_email@mail.ru']);

        $I->amGoingTo('grab new User and check if it was saved correctly');
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'new_email@mail.ru']);
        $I->assertEquals($user->getEmail(), $dbUser->getEmail());
        $I->assertEquals($user->getPassword(), $dbUser->getPassword());
        $I->assertEquals($user->getRoles(), $dbUser->getRoles());
    }


    /**
     * TODO: User with an empty password can not be saved
     * @param UnitTester $I
     */
    public function testSaveUserWithAnEmptyPassword(UnitTester $I): void
    {
        $I->amGoingTo('create a new User with an empty roles');
        $I->persistEntity(new User, [
            'email'    => 'other_email@mail.ru',
            'password' => '',
            'roles'    => ['ROLE_TEST']
        ]);
        $I->seeInRepository(User::class, ['email' => 'other_email@mail.ru']);

        $I->amGoingTo('check if the User with an empty password was saved');
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'other_email@mail.ru']);
        $I->assertEquals('', $dbUser->getPassword());

        $I->amGoingTo('check the field `password` in database is empty');
        $dbField = $I->grabFromRepository(User::class, 'password', ['email' => 'other_email@mail.ru']);
        $I->assertEquals('', $dbField);

    }


    /**
     * @param UnitTester $I
     */
    public function testSavedUserWithAnEmptyRolesHasRoleUser(UnitTester $I): void
    {
        $I->amGoingTo('create a new User with an empty roles');
        $I->persistEntity(new User, [
            'email'    => 'another_email@mail.ru',
            'password' => 'kitten',
            'roles'    => []
        ]);
        $I->seeInRepository(User::class, ['email' => 'another_email@mail.ru']);

        $I->amGoingTo('check if the saved user has `ROLE_USER`');
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'another_email@mail.ru']);
        $I->assertEquals(['ROLE_USER'], $dbUser->getRoles());

        $I->amGoingTo('check the field `roles` in database is empty');
        $dbField = $I->grabFromRepository(User::class, 'roles', ['email' => 'another_email@mail.ru']);
        $I->assertEquals('[]', $dbField);

    }


    /**
     * @param UnitTester $I
     */
    public function testSavedUserWithTheRoleUserHasOnlyOneUniqueRoleUser(UnitTester $I): void
    {
        $I->amGoingTo('create a new User with the `ROLE_USER`');
        $I->persistEntity(new User, [
            'email'    => 'any_email@mail.ru',
            'password' => 'kitten',
            'roles'    => ['ROLE_USER']
        ]);
        $I->seeInRepository(User::class, ['email' => 'any_email@mail.ru']);

        $I->amGoingTo('check if the `ROLE_USER` was no duplicated');
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'any_email@mail.ru']);
        $I->assertEquals(['ROLE_USER'], $dbUser->getRoles());

        $I->amGoingTo('check the field roles in database contains `ROLE_USER`');
        $dbField = $I->grabFromRepository(User::class, 'roles', ['email' => 'any_email@mail.ru']);
        $I->assertEquals('["ROLE_USER"]', $dbField);
    }


    /**
     * @param UnitTester $I
     */
    public function testSavedUserWithTheRoleRootHasRoleUser(UnitTester $I): void
    {
        $I->amGoingTo('create a new User with the `ROLE_ROOT');
        $I->persistEntity(new User, [
            'email'    => 'any@mail.ru',
            'password' => 'kitten',
            'roles'    => ['ROLE_ROOT']
        ]);
        $I->seeInRepository(User::class, ['email' => 'any@mail.ru']);

        $I->amGoingTo('check if the saved user has `ROLE_USER`');
        $dbUser = $I->grabEntityFromRepository(User::class, ['email' => 'any@mail.ru']);
        $I->assertEquals(['ROLE_ROOT', 'ROLE_USER'], $dbUser->getRoles());

        $I->amGoingTo('check the field roles in database contains `ROLE_ROOT`');
        $dbField = $I->grabFromRepository(User::class, 'roles', ['email' => 'any@mail.ru']);
        $I->assertEquals('["ROLE_ROOT"]', $dbField);
    }
}
