<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\KlarnaSandboxPaymentPage;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\AdyenNotSuccessfulPage;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\CheckoutAdyenPage;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\_support\Traits\OrderHistory;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

/**
 * @group klarna
 * @group osc_adyen
 */
final class KlarnaCest extends BaseCest
{
    use OrderHistory;

    private CheckoutAdyenPage $checkoutPage;
    private KlarnaSandboxPaymentPage $klarnaSandboxPaymentPage;
    private AdyenNotSuccessfulPage $adyenNotSuccessfulPage;

    public function _before(AcceptanceTester $I): void
    {
        parent::_before($I);
        $this->checkoutPage = new CheckoutAdyenPage($I);
        $this->klarnaSandboxPaymentPage = new KlarnaSandboxPaymentPage($I);
        $this->adyenNotSuccessfulPage = new AdyenNotSuccessfulPage($I);
    }

    public function checkOrderKlarnaAuthorised(AcceptanceTester $I): void
    {
        $I->wantToTest("Klarna authorised (Adyen) on Frontend order");

        $this->prepareTest();
        $this->checkoutPage->clickAdyenPayButton(Module::PAYMENT_KLARNA_ID);

        $this->klarnaSandboxPaymentPage->clickBuyButtonAndConfirm();

        $thankYouPage = $this->_checkSuccessfulPayment();
        $this->orderNumber = $thankYouPage->grabOrderNumber();
        $this->checkOrderHistory($I);
    }

    public function checkOrderKlarnaCancelled(AcceptanceTester $I): void
    {
        $I->wantToTest("Klarna cancelled (Adyen) on Frontend order");

        $this->prepareTest();
        $this->checkoutPage->clickAdyenPayButton(Module::PAYMENT_KLARNA_ID);

        $this->klarnaSandboxPaymentPage->clickBuyButtonAndCancel();

        $this->adyenNotSuccessfulPage->checkMessage(Module::ADYEN_RETURN_RESULT_CODE_CANCELLED);
    }

    protected function _getOXID(): array
    {
        return [Module::PAYMENT_KLARNA_ID];
    }

    protected function _getPaymentId(): string
    {
        return Module::PAYMENT_KLARNA_ID;
    }

    private function prepareTest(): void
    {
        $this->_initializeTest();
        $this->_choosePayment();
    }
}
