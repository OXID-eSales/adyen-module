<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\_support\Traits;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

trait OrderHistory
{
    /**
     * use this trait to check the appearance of the order in the history
     * Class using this trait mus provide the following properties
     * - $orderNumber (as retrieved by BaseCest::_checkSuccessfulPayment())
     * - $placeholderPaymentId (the placeholder for translated text of the payment method)
     * @param AcceptanceTester $I
     * @return void
     */
    protected function checkOrderHistory(AcceptanceTester $I): void
    {
        // both properties are expected to exist in the using class
        $orderNumber = $this->orderNumber;
        $placeholderPaymentId = $this->placeholderPaymentId;

        // Database updates
        $I->updateInDatabase(
            'oxorder',
            ['ADYENPSPREFERENCE' => $orderNumber],
            ['OXORDERNR' => $orderNumber]
        );

        $langAbbr = Registry::getLang()->getLanguageAbbr();
        $paymentName = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions'][$langAbbr]['desc'];

        // Check order history
        $orderHistoryPage = new UserOrderHistory($I);
        $I->amOnPage($orderHistoryPage->URL);

        $I->makeScreenshot(date('Y-m-d_His') . ' Order History ' . $placeholderPaymentId);

        $I->waitForText(
            Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_PAYMENT_NOTE") . ': ' .
            $paymentName
        );
        $I->waitForText(
            Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_REFERENCE_NOTE") . ': ' .
            $orderNumber
        );
    }
}
