<?php

namespace App\Tests\Functional\Controller;

use App\Tests\FunctionalTester;
use App\Tests\Page\Account;
use App\Tests\Page\Components\LocaleSwitcher;

/**
 * Class AccountControllerCest
 * @package App\Tests\Functional\Controller
 */
class AccountControllerCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testAccountPageContent(FunctionalTester $I): void
    {
        $vars = self::getVars();
        $I->amOnPage($vars['url']);

        $I->amGoingTo('see Account page content');
        $I->seeResponseCodeIsSuccessful();
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
