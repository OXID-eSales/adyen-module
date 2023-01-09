<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

/**
 * @group CreditCard
 */
final class CreditCardCest extends BaseCest
{
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
        $orderNumber = $this->_checkSuccessfulPayment();

        $I->updateInDatabase(
            'oxorder',
            ['ADYENPSPREFERENCE' => $orderNumber],
            ['OXORDERNR' => $orderNumber]
        );

        $orderHistoryPage = new UserOrderHistory($I);
        $I->amOnPage($orderHistoryPage->URL);
        $I->makeScreenshot(time() . ' Order History');
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_PAYMENT_NOTE")
            . ': ' . Translator::translate("OSC_ADYEN_PAYMENT_METHOD_CREDITCARD"));
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_REFERENCE_NOTE")
            . ': ' . $orderNumber);
    }
}
