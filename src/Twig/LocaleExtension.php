<?php

namespace App\Twig;

use Symfony\Component\Intl\Intl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This Twig extension adds a new `getLocales()` function
 * to easily get app locales list with each locale written in its own language
 * @see https://symfony.com/doc/current/cookbook/templating/twig_extension.html
 *
 * Class LocaleExtension
 * @package App\Twig
 */
class LocaleExtension extends AbstractExtension
{
    /** @var array $localeCodes */
    private $localeCodes;

    /** @var array $locales */
    private $locales;


    /**
     * LocaleExtension constructor
     *
     * @param string $appLocales
     */
    public function __construct(string $appLocales)
    {
        $this->localeCodes = explode('|', $appLocales);
    }


    /**
     * @return array
     */
    final public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'locales',
                [$this, 'getLocales']
            ),
        ];
    }


    /**
     * Takes the list of codes of the locales
     * and returns an array with the name of each locale
     * written in its own language (e.g. English, Español, etc.)
     *
     * @return array
     */
    final public function getLocales(): array
    {
        if (null !== $this->locales) {
            return $this->locales;
        }

        $this->locales = [];

        foreach ($this->localeCodes as $localeCode) {
            $this->locales[] = [
                'code' => $localeCode,
                'name' => Intl::getLocaleBundle()->getLocaleName(
                    $localeCode, $localeCode
                )
            ];
        }

        return $this->locales;
    }
}
