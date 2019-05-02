<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\FunctionalTester;
use App\Tests\Page\Tasklist;
use DateTime;

/**
 * Class TaskDeleteCest
 * @package App\Tests\Functional\Controller\Task
 */
class TaskDeleteCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testDeleteUser(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new Task to the Database');
        $task = new Task();
        $I->persistEntity($task, [
            'title' => 'Do something',
            'dateDeadline' => DateTime::createFromFormat('m/d/Y', '1/10/2014'),
            'state' => 'In progress'
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Delete` link');
        $I->see('Do something');
        $I->click("a[href=\"/en/task/".$task->getId()."/delete/confirm\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/task/".$task->getId()."/delete/confirm");
        $I->click($vars['delete_button']);

        $I->amGoingTo('see new task in the Tasklist');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals($vars['url']);
        $I->dontSee('Do something');
        $I->dontSeeInRepository(Task::class, ['title' => 'Do something']);
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url' => Tasklist::$url['en'],
            'delete_button' => Tasklist::$delete['delete_permanently_button'],
        ];
    }
}
