<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\AdyenNotSuccessfulPage;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\CheckoutAdyenPage;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\CommonPage;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\CommonPageCurrency;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\TwintSandboxPaymentPage;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\_support\Traits\OrderHistory;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

/**
 * @group twint
 * @group osc_adyen
 */
final class TwintCest extends BaseCest
{
    use OrderHistory;

    public function checkOrderTwintAuthorised(AcceptanceTester $I): void
    {
        $I->wantToTest("Twint authorised (Adyen) on Frontend order");

        $this->prepareTwintTest($I);
        $this->clickPayButton($I, Module::PAYMENT_TWINT_ID);

        $this->clickTwintSandboxButton($I);

        $thankYouPage = $this->_checkSuccessfulPayment();
        $this->orderNumber = $thankYouPage->grabOrderNumber();
        $this->checkOrderHistory($I);
    }

    public function checkOrderTwintCancelled(AcceptanceTester $I): void
    {
        $I->wantToTest("Twint cancelled (Adyen) on Frontend order");

        $this->checkOrderTwintNotSuccessful(
            $I,
            TwintSandboxPaymentPage::RESULT_CANCELLED,
            Module::ADYEN_RETURN_RESULT_CODE_CANCELLED
        );
    }

    public function checkOrderTwintRefused(AcceptanceTester $I): void
    {
        $I->wantToTest("Twint refused (Adyen) on Frontend order");

        $this->checkOrderTwintNotSuccessful(
            $I,
            TwintSandboxPaymentPage::RESULT_REFUSED,
            Module::ADYEN_RETURN_RESULT_CODE_REFUSED
        );
    }

    public function checkOrderTwintError(AcceptanceTester $I): void
    {
        $I->wantToTest("Twint error (Adyen) on Frontend order");

        $this->checkOrderTwintNotSuccessful(
            $I,
            TwintSandboxPaymentPage::RESULT_ERROR,
            Module::ADYEN_RETURN_RESULT_CODE_ERROR
        );
    }

    private function checkOrderTwintNotSuccessful(
        AcceptanceTester $I,
        string $twintSandboxPageResult,
        string $resultCode
    ): void {
        $this->prepareTwintTest($I);
        $this->clickPayButton($I, Module::PAYMENT_TWINT_ID);

        $this->clickTwintSandboxButton($I, $twintSandboxPageResult);

        $this->checkUnSuccessfulPayment($I, $resultCode);
    }

    protected function _getOXID(): array
    {
        return [Module::PAYMENT_TWINT_ID];
    }

    protected function _getPaymentId(): string
    {
        return "payment_" . Module::PAYMENT_TWINT_ID;
    }

    private function selectCurrencyCHF(AcceptanceTester $I): void
    {
        $commonPage = new CommonPage($I);
        $commonPage->selectCurrency(CommonPageCurrency::CURRENCY_CHF);
    }
    private function clickPayButton(AcceptanceTester $I, $paymentId = Module::PAYMENT_PAYPAL_ID)
    {
        $checkoutPage = new CheckoutAdyenPage($I);
        $checkoutPage->clickAdyenPayButton($paymentId);
    }

    private function prepareTwintTest(AcceptanceTester $I): void
    {
        $this->_initializeTest();
        $this->selectCurrencyCHF($I);

        $this->_choosePayment();
    }

    private function clickTwintSandboxButton(
        AcceptanceTester $I,
        $result = TwintSandboxPaymentPage::RESULT_AUTHORISED
    ): void {
        $twintSandboxPaymentPage = new TwintSandboxPaymentPage($I);
        $twintSandboxPaymentPage->clickTwintSandboxButton($result);
    }

    private function checkUnSuccessfulPayment(AcceptanceTester $I, string $resultCode): void
    {
        $notSuccessfulPage = new AdyenNotSuccessfulPage($I);
        $notSuccessfulPage->checkMessage($resultCode);
    }
}
