<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

final class AdyenPaymentCest extends BaseCest
{
    protected function _getOXID(): array
    {
        return [Module::STANDARD_PAYMENT_ID];
    }

    protected function _getPaymentId(): string
    {
        return "payment_oscadyen";
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    public function checkOrderInFrontendListOrder(AcceptanceTester $I)
    {
        $I->wantToTest(" to check order in frontend list order");
        $this->_initializeTest();
        $orderPage = $this->_choosePayment();
        $orderPage->submitOrder();

        $orderNumber = $this->_checkSuccessfulPayment();

        $I->updateInDatabase(
            'oxorder',
            ['ADYENPSPREFERENCE' => $orderNumber],
            ['OXORDERNR' => $orderNumber]
        );

        $orderHistoryPage = new UserOrderHistory($I);
        $I->amOnPage($orderHistoryPage->URL);

        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_PAYMENT_NOTE") . ' Adyen');
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_REFERENCE_NOTE") .
            ' ' . $orderNumber);
    }
}
