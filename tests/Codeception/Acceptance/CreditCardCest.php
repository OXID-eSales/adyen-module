<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\_support\Traits\OrderHistory;

/**
 * @group CreditCard
 * @group osc_adyen
 */
final class CreditCardCest extends BaseCest
{
    use OrderHistory;

    protected $placeholderPaymentId;

    protected function _getOXID(): array
    {
        return [Module::PAYMENT_CREDITCARD_ID];
    }

    protected function _getPaymentId(): string
    {
        return Module::PAYMENT_CREDITCARD_ID;
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
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    private function _submitCreditCardPayment(AcceptanceTester $I)
    {
        $orderPage = $this->_fillCreditCardDetails();
        $orderPage->submitOrder();
    }

    public function checkOrderCreditCard(AcceptanceTester $I)
    {
        $this->_initializeTest();
        $this->_submitCreditCardPayment($I);
        $thankYouPage = $this->_checkSuccessfulPayment();
        $this->orderNumber = $thankYouPage->grabOrderNumber();
    }
}
