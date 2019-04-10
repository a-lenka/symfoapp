<?php

namespace App\Tests\Acceptance\Pages;

use App\Tests\AcceptanceTester;
use App\Tests\Page\Components\LocaleSwitcher;
use App\Tests\Page\Home;

/**
 * Class HomePageCest
 * @package App\Tests\Acceptance\Pages
 */
class HomePageCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function testSwitchLocaleToRussian(AcceptanceTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->see($vars['header_text'], $vars['header_tag']);

        $I->amGoingTo('click Russian link and check the Response');
        $I->click($vars['locale_trigger']);
        $I->click($vars['ru_link'], $vars['locales_box_id']);

        $I->amGoingTo('see Russian content');
        $I->seeCurrentUrlEquals($vars['ru_url']);
        $I->see($vars['ru_header_text'], $vars['header_tag']);
    }


    /**
     * @param AcceptanceTester $I
     */
    public function testSwitchLocaleToEnglish(AcceptanceTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);
        $I->see($vars['header_text'], $vars['header_tag']);

        $I->amGoingTo('click English link and check the Response');
        $I->click($vars['locale_trigger']);
        $I->click($vars['en_link'], $vars['locales_box_id']);

        $I->amGoingTo('see English content');
        $I->seeCurrentUrlEquals($vars['url']);
        $I->see($vars['header_text'], $vars['header_tag']);
    }


    /**
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
            'locale_trigger' => LocaleSwitcher::$trigger['text'],
            'locales_box_id' => LocaleSwitcher::$links['container_id'],
            'ru_link'        => LocaleSwitcher::$links['ru_text'],
            'en_link'        => LocaleSwitcher::$links['en_text'],
        ];
    }
}
