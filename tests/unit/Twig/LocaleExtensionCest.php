<?php

namespace App\Tests\Unit\Twig;

use App\Tests\UnitTester;
use App\Twig\LocaleExtension;

/**
 * Class LocaleExtensionCest
 * @package App\Tests\Unit\Twig
 */
class LocaleExtensionCest
{
    /**
     * @param UnitTester $I
     */
    public function testLocaleExtensionHasEnglishLocale(UnitTester $I): void
    {
        $I->amGoingTo('create new Locale Extension');
        $extension = new LocaleExtension('en|ru');
        $locales = $extension->getLocales();
        $I->assertCount(2, $locales);

        $I->amGoingTo('check if an English locale exists');
        $I->assertCount(2, $locales);
        $I->assertEquals('en', $locales[0]['code']);
        $I->assertEquals('English', $locales[0]['name']);
    }


    /**
     * @param UnitTester $I
     */
    public function testLocaleExtensionHasRussianLocale(UnitTester $I): void
    {
        $I->amGoingTo('create new Locale Extension');
        $extension = new LocaleExtension('en|ru');
        $locales = $extension->getLocales();
        $I->assertCount(2, $locales);

        $I->amGoingTo('check if the Russian locale exists');
        $I->assertEquals('ru', $locales[1]['code']);
        $I->assertEquals('русский', $locales[1]['name']);
    }
}
