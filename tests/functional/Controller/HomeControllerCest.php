<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTester;
use App\Tests\Page\Home;

/**
 * Class HomeControllerCest
 * @package App\Tests\Controller
 */
class HomeControllerCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testHomePageContent(FunctionalTester $I): void
    {
        $vars = Home::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('see Home page content');
        $I->seeResponseCodeIsSuccessful();
        $I->see($vars['header_text'], $vars['header_tag']);
    }
}
