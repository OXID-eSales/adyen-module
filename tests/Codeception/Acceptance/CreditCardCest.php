<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

/**
 * @group unzer_module
 * @group ThirdGroup
 */
final class CreditCardCest extends BaseCest
{
    public function firstTest(AcceptanceTester $I): void
    {
        $I->wantToTest('Hello World');
    }

    protected function _getOXID(): array
    {
        return [Module::STANDARD_PAYMENT_ID];
    }

    protected function _getPaymentId(): string
    {
        return Module::STANDARD_PAYMENT_ID;
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    private function _prepareCreditCardTest(AcceptanceTester $I)
    {
        $this->_initializeTest();
    }

    /**
     * @param string $name Fixtures name
     * @return void
     */
    private function _submitCreditCardPayment(string $name)
    {
        $orderPage = $this->_choosePayment($this->cardPaymentLabel);

        $fixtures = Fixtures::get($name);
    }
}
