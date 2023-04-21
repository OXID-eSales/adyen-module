<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

class CommonPage extends Page
{
    public function selectCurrency(string $currency = CommonPageCurrency::CURRENCY_EUR)
    {
        $commonPageCurrency = new CommonPageCurrency();
        $commonPageCurrency->selectCurrency($this->I, $currency);
    }
}
