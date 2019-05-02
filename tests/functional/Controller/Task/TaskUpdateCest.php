<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\FunctionalTester;
use App\Tests\Page\Tasklist;
use DateTime;

/**
 * Class TaskUpdateCest
 * @package App\Tests\Functional\Controller\Task
 */
class TaskUpdateCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testUpdateTaskTitle(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new Task to the Database');
        $task = new Task();
        $I->persistEntity($task, [
            'title' => 'Old task title',
            'dateDeadline' => DateTime::createFromFormat('m-d-Y', '1-10-2014'),
            'state' => 'In progress'
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Update` link');
        $I->see('Old task title');
        $I->click("a[href=\"/en/task/".$task->getId()."/update\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/task/".$task->getId()."/update");

        $I->amGoingTo('update Task title');
        $I->fillField($vars['title_field'], 'New task title');
        $I->fillField($vars['deadline_date_field'], '2000-01-01');
        $I->selectOption('select', 'Done');
        $I->click($vars['submit_button']);

        $I->amGoingTo('see new Task title in the Tasklist');
        $I->dontSee('Old task title');
        $I->see('New task title');
    }


    /**
     * @param FunctionalTester $I
     */
    public function testUpdateTaskDeadlineDateWithValidDate(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new Task to the Database');
        $task = new Task();
        $I->persistEntity($task, [
            'title'        => 'Do something',
            'dateDeadline' => DateTime::createFromFormat('Y-m-d', '2000-01-30'),
            'state'        => 'In progress'
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Update` link');
        $I->see('Do something');
        $I->click("a[href=\"/en/task/".$task->getId()."/update\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/task/".$task->getId()."/update");

        $I->amGoingTo('update Task deadline');

        $I->fillField($vars['deadline_date_field'], '3000-01-30');
        $I->fillField($vars['deadline_time_field'], '00:00');
        $I->click($vars['submit_button']);
        $I->dontSee('This value is not valid');

        $I->amGoingTo('see new Task title in the Tasklist');
        $I->seeCurrentUrlEquals('/en/task/list/all');
        $I->seeElement('table');
        $I->dontSee('3000-01-30');
        $I->comment('Maybe page must be reloaded?');

        $I->amGoingTo('grab Task from the database and check the deadline was not updated');
        $dbTask = $I->grabEntityFromRepository(Task::class, ['title' => 'Do something']);
        $I->assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', '3000-01-30 00:00:00'),
            $dbTask->getDateDeadline()
        );

        $I->assertEquals(
            '3000-01-30 00:00:00',
            $dbTask->getDateDeadline()->format('Y-m-d H:i:s')
        );

        $I->amGoingTo('see the new deadline is in the Tasklist');
        $I->amOnPage($vars['url']);
        $I->see('Do something');
        $I->see('30-01-3000');
        $I->dontSee('30-01-2000');
    }


    /**
     * @param FunctionalTester $I
     */
    public function testUpdateTaskDeadlineDateWithWrongDateFormat(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->amGoingTo('save new Task to the Database');
        $task = new Task();
        $I->persistEntity($task, [
            'title' => 'Do something',
            // Date is saved in the wrong format,
            // but the DateTime obj will be formatted normally for the Database in any case
            'dateDeadline' => DateTime::createFromFormat('d-m-Y', '30-01-2000'),
            'state' => 'In progress'
        ]);

        $I->amOnPage($vars['url']);

        $I->am('Tester');
        $I->amGoingTo('click `Update` link');
        $I->see('Do something');
        $I->click("a[href=\"/en/task/".$task->getId()."/update\"]");
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals("/en/task/".$task->getId()."/update");

        $I->amGoingTo('update Task deadline');

        $I->fillField($vars['deadline_date_field'], '30-01-3000');
        $I->fillField($vars['deadline_time_field'], '00:00');
        $I->click($vars['submit_button']);

        $I->amGoingTo('see new Task title in the Tasklist');
        $I->dontSee('30-01-3000');
        $I->comment('This was a date, saved as 30-01-3000 but it should be the 3000-01-30 string');
        $I->comment('So I can\'t see the invalid value and see error message from Symfony');
        $I->see('This value is not valid');

        $I->amGoingTo('grab Task from the database and check the deadline was not updated');
        $dbTask = $I->grabEntityFromRepository(Task::class, ['title' => 'Do something']);
        $I->assertEquals( // DateTime object will be formatted in the right way
            DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-30 00:00:00'),
            $dbTask->getDateDeadline()
        );

        $I->assertEquals(
            '2000-01-30 00:00:00',
            $dbTask->getDateDeadline()->format('Y-m-d H:i:s')
        );

        // DB format       'Y-m-d\TH:i:s' '2001-10-30 20:00:00'
        // Symfony format  'Y-m-d\TH:i:s' '2001-10-30 20:00:00'
        // Materialize CSS do not works here, because JS ('yyyy-mm-dd')
        // On the page there are 30-10-2000 and 30-10-2001
        $I->amGoingTo('see the new deadline is absent in the Tasklist');
        $I->amOnPage($vars['url']);
        $I->see('Do something');
        $I->see('30-01-2000');     // The old date was not updated
        $I->dontSee('30-01-3000'); // The new date was not inserted
        // DB saves wrong date in such a case, but Symfony do not allows save wrong date to DB
        $I->dontSee('30-11--0001');
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url'         => Tasklist::$url['en'],
            'title_field' => Tasklist::$form['title_field_text'],
            'deadline_date_field' => Tasklist::$form['deadline_date_field_text'],
            'deadline_time_field' => Tasklist::$form['deadline_time_field_text'],
            'submit_button'       => Tasklist::$form['submit_button'],
        ];
    }
}
