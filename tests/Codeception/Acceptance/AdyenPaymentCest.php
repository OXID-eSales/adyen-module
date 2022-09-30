<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

/**
 * @group osc_adyen
 */
final class AdyenPaymentCest extends BaseCest
{
    protected function _getOXID(): array
    {
        return [Module::STANDARD_PAYMENT_ID];
    }

    protected function _getPaymentId(): string
    {
        return "payment_" . Module::STANDARD_PAYMENT_ID;
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

        $langCode = Registry::getLang()->getLanguageAbbr();
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_PAYMENT_NOTE")
            . ': ' . Module::PAYMENT_DEFINTIONS[Module::STANDARD_PAYMENT_ID]['descriptions'][$langCode]['desc']);
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_REFERENCE_NOTE")
            . ': ' . $orderNumber);
    }
}
