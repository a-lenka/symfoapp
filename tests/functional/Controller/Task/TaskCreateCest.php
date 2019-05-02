<?php

namespace App\Tests\Functional\Controller\Task;

use App\Tests\FunctionalTester;
use App\Tests\Page\Tasklist;

/**
 * Class TaskCreateCest
 * @package App\Tests\Functional\Controller\Task
 */
class TaskCreateCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testCreateUser(FunctionalTester $I): void
    {
        $vars = self::getVars();

        $I->am('Tester');
        $I->amOnPage($vars['url']);

        $I->amGoingTo('click `Create` link');
        $I->click($vars['create_button']);
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals($vars['create_url']);

        $I->amGoingTo('create new Task');
        $I->fillField($vars['title_field_text'], 'New task');
        $I->fillField($vars['deadline_date_field'],'2000-01-01');
        $I->fillField($vars['deadline_time_field'],'10:15');
        $I->click($vars['submit_button']);

        $I->amGoingTo('see new task in the Tasklist');
        $I->amOnPage($vars['url']);
        $I->see('New task');
        $I->see('01-01-2000');
    }


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url'          => Tasklist::$url['en'],
            'create_url'   => Tasklist::$url['create'],
            'create_button'=> Tasklist::$create['create_btn_icon_text'],
            'title_field_text' => Tasklist::$form['title_field_text'],
            'deadline_date_field' => Tasklist::$form['deadline_date_field_text'],
            'deadline_time_field' => Tasklist::$form['deadline_time_field_text'],
            'submit_button' => Tasklist::$form['submit_button'],
        ];
    }
}
