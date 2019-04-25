<?php

namespace App\Tests\Functional\Controller\Home;

use App\Tests\FunctionalTester;
use App\Tests\Page\Components\LocaleSwitcher;
use App\Tests\Page\Home;

/**
 * Class HomeControllerCest
 * @package App\Tests\Functional\Controller\Home
 */
class HomeControllerCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testHomePageContent(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('see Home page content');
        $I->seeResponseCodeIsSuccessful();
        $I->see($vars['header_text'], $vars['header_tag']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testSwitchLocaleToRussian(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('click Russian link and check the Response');
        $I->click($vars['ru_link']);
        $I->seeResponseCodeIsSuccessful();

        $I->amGoingTo('see Russian content');
        $I->seeCurrentUrlEquals($vars['ru_url']);
        $I->see($vars['ru_header_text'], $vars['header_tag']);
    }


    /**
     * @param FunctionalTester $I
     */
    public function testSwitchLocaleToEnglish(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('click English link and check the Response');
        $I->click($vars['en_link']);
        $I->seeResponseCodeIsSuccessful();

        $I->amGoingTo('see English content');
        $I->seeCurrentUrlEquals($vars['url']);
        $I->see($vars['header_text'], $vars['header_tag']);
    }


    /**
     * NOTE: 1. I have not used `get_class_vars()` because PHPStorm do not hint it values.
     *       2. `getVars()` is here, because if I do not want to wrestle
     *              with how to paste in tests an additional portion of data
     *              saving some readability.
     *       3. All the possible changes are only here.
     *       4. Symfony translator is cheerful guy :)
     *
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url'            => Home::$url['en'],
            'ru_url'         => Home::$url['ru'],
            'header_text'    => Home::$header['text'],
            'ru_header_text' => Home::$header['ru_text'],
            'header_tag'     => Home::$header['tag'],
            'ru_link'        => LocaleSwitcher::$links['ru_text'],
            'en_link'        => LocaleSwitcher::$links['en_text'],
        ];
    }
}
