<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Tests\UnitTester;
use DateTime;

/**
 * Class TaskEntityCest
 * @package App\Tests\Unit\Entity
 */
class TaskEntityCest
{
    /**
     * @param UnitTester $I
     */
    public function validTaskWasSavedSuccessfully(UnitTester $I): void
    {
        $I->amGoingTo('save new valid Task to the Database');
        $task = new Task();
        $I->persistEntity($task, [
            'title' => 'Do something',
            'dateDeadline' => DateTime::createFromFormat('m-d-Y', '1-10-2014'),
            'state' => 'In progress'
        ]);
        $I->seeInRepository(Task::class, ['title' => 'Do something']);

        $I->amGoingTo('grab new Task and check if it was saved correctly');
        $dbTask = $I->grabEntityFromRepository(Task::class, ['title' => 'Do something']);
        $I->assertEquals($task->getTitle(), $dbTask->getTitle());
        $I->assertEquals($task->getDateDeadline(), $dbTask->getDateDeadline());
        $I->assertEquals($task->getState(), $dbTask->getState());
    }
}
