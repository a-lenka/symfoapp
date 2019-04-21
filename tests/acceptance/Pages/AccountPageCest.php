<?php

namespace App\Tests\Acceptance\Pages;

use App\Tests\AcceptanceTester;
use App\Tests\Page\Account;
use App\Tests\Page\Components\LocaleSwitcher;

/**
 * Class AccountPageCest
 * @package App\Tests\Acceptance\Pages
 */
class AccountPageCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function testAccountContent(AcceptanceTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('see Account page content');
        $I->see($vars['header_text'], $vars['header_tag']);
    }

    /** TODO: Write tests for Account page when WebDriver will be work again */


    /**
     * @return array
     */
    private static function getVars(): array
    {
        return [
            'url'            => Account::$url['en'],
            'ru_url'         => Account::$url['ru'],
            'header_text'    => Account::$header['text'],
            'ru_header_text' => Account::$header['ru_text'],
            'header_tag'     => Account::$header['tag'],
            'ru_link'        => LocaleSwitcher::$links['ru_text'],
            'en_link'        => LocaleSwitcher::$links['en_text'],
        ];
    }
}
