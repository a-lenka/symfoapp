<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\FunctionalTester;
use App\Tests\Page\Home;
use App\Tests\Page\Tasklist;
use DateTime;
use Exception;

/**
 * Class UserShowListCest
 * @package App\Tests\Functional\Controller\User
 */
class TaskShowListCest
{
    /**
     * @param FunctionalTester $I
     * @throws Exception
     */
    public function testShowAllTasks(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('save new Task to the Database');
        $first = new Task();
        $I->persistEntity($first, [
            'title'        => 'do first',
            'dateDeadline' => new DateTime('2019-02-24'),
            'state'        => 'in progress'
        ]);
        $I->seeInRepository(Task::class, ['title' => 'do first']);


        $second = new Task();
        $I->persistEntity($second, [
            'title'        => 'do second',
            'dateDeadline' => new DateTime('2019-02-25'),
            'state'        => 'done',
        ]);
        $I->seeInRepository(Task::class, ['title' => 'do second']);


        $third = new Task();
        $I->persistEntity($third, [
            'title'        => 'do third',
            'dateDeadline' => new DateTime('2019-02-26'),
            'state'        => 'next',
        ]);
        $I->seeInRepository(Task::class, ['title' => 'do third']);


        $I->am('Tester');
        $I->amGoingTo('see all saved tasks in the Tasklist');
        $I->click($vars['task_link']);
        $I->seeCurrentUrlEquals($vars['tasklist_url']);

        $I->see('do first');
        $I->see('do second');
        $I->see('do third');
    }


    /**
     * @param FunctionalTester $I
     * @throws Exception
     */
    public function testSortTasksByTitle(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('save new Tasks to the Database');
        $first = new Task();
        $I->persistEntity($first, [
            'title'        => 'aaa',
            'dateDeadline' => new DateTime('2019-01-20'),
            'state'        => 'next',
        ]);
        $I->seeInRepository(Task::class, ['title' => 'aaa']);


        $last = new Task();
        $I->persistEntity($last, [
            'title'        => 'zzz',
            'dateDeadline' => new DateTime('2019-01-20'),
            'state'        => 'next',
        ]);
        $I->seeInRepository(Task::class, ['title' => 'zzz']);


        $I->am('Tester');
        $I->amGoingTo('see all saved tasks in the Tasklist');
        $I->click($vars['task_link']);
        $I->seeCurrentUrlEquals($vars['tasklist_url']);
        $I->see('aaa');
        $I->see('zzz');

        $I->amGoingTo('sort Tasks with desc order');
        $I->click('a[href="'.$vars['title_desc'].'"]');
        $I->see('zzz', 'table tbody tr:first-child');

        $I->amGoingTo('sort Task with asc order');
        $I->click('a[href="'.$vars['title_asc'].'"]');
        $I->see('aaa', 'table tbody tr:first-child');
    }


    /**
     * @param FunctionalTester $I
     * @throws Exception
     */
    public function testSortTasksByDeadline(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['home_url']);

        $I->amGoingTo('save new Tasks to the Database');
        $first = new Task();
        $I->persistEntity($first, [
            'title'        => 'do something',
            'dateDeadline' => new DateTime('2000-01-10'),
            'state'        => 'next',
        ]);
        $I->seeInRepository(Task::class, ['dateDeadline' => '2000-01-10']);


        $last = new Task();
        $I->persistEntity($last, [
            'title'        => 'do something else',
            'dateDeadline' => new DateTime('3000-01-10'),
            'state'        => 'next',
        ]);
        $I->seeInRepository(Task::class, ['dateDeadline' => '3000-01-10']);


        $I->am('Tester');
        $I->amGoingTo('see all saved tasks in the Tasklist');
        $I->click($vars['task_link']);
        $I->seeCurrentUrlEquals($vars['tasklist_url']);
        $I->see('10-01-3000');
        $I->see('10-01-3000');

        $I->amGoingTo('sort Tasks with desc order');
        $I->click('a[href="'.$vars['deadline_desc'].'"]');
        $I->see('10-01-3000', 'table tbody tr:first-child');

        $I->amGoingTo('sort Task with asc order');
        $I->click('a[href="'.$vars['deadline_asc'].'"]');
        $I->see('10-01-2000', 'table tbody tr:first-child');
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'home_url'    => Home::$url['en'],
            'tasklist_url'=> Tasklist::$url['en'],
            'header_text' => Tasklist::$header['text'],
            'header_tag'  => Tasklist::$header['tag'],
            'task_link'   => Tasklist::$links['topmenu_link_text'],
            'title_asc'   => Tasklist::$sortLinks['title_asc'],
            'title_desc'  => Tasklist::$sortLinks['title_desc'],
            'deadline_asc'   => Tasklist::$sortLinks['deadline_asc'],
            'deadline_desc'  => Tasklist::$sortLinks['deadline_desc'],
            'state_asc'   => Tasklist::$sortLinks['state_asc'],
            'state_desc'  => Tasklist::$sortLinks['state_desc'],
        ];
    }
}
