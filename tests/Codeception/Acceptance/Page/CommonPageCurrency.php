<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

use Codeception\Actor;

class CommonPageCurrency
{
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_GBP = 'GBP';
    public const CURRENCY_CHF = 'CHF';
    public const CURRENCY_USD = 'USD';

    private const CURRENCY_BUTTON_SELECTOR = '.currencies-menu .btn';
    private const CURRENCY_LINK_EUR_SELECTOR = '.currencies-menu a[title=*]';

    public function selectCurrency(Actor $I, string $currency = self::CURRENCY_EUR)
    {
        $I->waitForElement(self::CURRENCY_BUTTON_SELECTOR, 30);
        $I->click(self::CURRENCY_BUTTON_SELECTOR);
        $currencyLinkSelector = $this->getSelectorForCurrencyLink($currency);
        $I->waitForElement($currencyLinkSelector, 30);
        $I->click($currencyLinkSelector);
        $I->waitForElementNotVisible($currencyLinkSelector);
    }

    private function getSelectorForCurrencyLink(string $currency): string
    {
        return str_replace('*', $currency, self::CURRENCY_LINK_EUR_SELECTOR);
    }
}
