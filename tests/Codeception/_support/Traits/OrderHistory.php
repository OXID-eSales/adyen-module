<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\_support\Traits;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidEsales\EshopCommunity\Application\Model\Payment;
use OxidEsales\EshopCommunity\Core\Registry;
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

        // always english!
        $langId = 1;
        // page "Check order history"
        $I->amOnPage('index.php?cl=account_order&lang=' . $langId);

        // get the payment description from database
        $oPayment = oxNew(Payment::class);
        $oPayment->loadInLang($langId, $placeholderPaymentId);
        $paymentName = $oPayment->oxpayments__oxdesc->value;

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
