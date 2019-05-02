<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\Task;
use App\Tests\FunctionalTester;
use App\Tests\Page\Tasklist;
use DateTime;

/**
 * Class TaskShowDetailsCest
 * @package App\Tests\Functional\Controller\Task
 */
class TaskShowDetailsCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testShowTaskDetails(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new Task to the Database');
        $task = new Task();
        $I->persistEntity($task, [
            'title' => 'Do something',
            'dateDeadline' => DateTime::createFromFormat('m-d-Y', '01-10-2014'),
            'state' => 'In progress'
        ]);
        $dbTask = $I->grabEntityFromRepository(Task::class, ['title' => 'Do something']);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('find saved task in the Tasklist and see it details');
        $I->see($dbTask->getId());
        $I->see($dbTask->getDateDeadline()->format('Y'));
        $I->see($dbTask->getState());

        $I->click("a[href=\"/en/task/".$task->getId()."/details\"]");
        $I->seeCurrentUrlEquals("/en/task/".$task->getId()."/details");
        $I->seeNumberOfElements( 'tbody tr', 1);
        $I->dontSeeElement('h1');
        $I->see($dbTask->getTitle(), 'h4');
        $I->see($dbTask->getState(), 'td');
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url'         => Tasklist::$url['en'],
            'header_text' => Tasklist::$header['text'],
            'header_tag'  => Tasklist::$header['tag'],
        ];
    }
}
