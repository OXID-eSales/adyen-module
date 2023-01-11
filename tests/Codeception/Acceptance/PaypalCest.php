<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

/**
 * @group osc_adyen
 */
final class PaypalCest extends BaseCest
{
    protected AcceptanceTester $user;
    protected string $spinner = '#spinner';
    protected string $globalSpinner = "//div[@data-testid='global-spinner']";
    protected string $preloaderSpinner = "//div[@id='preloaderSpinner']";
    protected string $loginSection = '#loginSection';
    protected string $gdprContainer = "#gdpr-container";
    protected string $gdprCookieBanner = "#gdprCookieBanner";
    protected string $acceptAllPaypalCookies = "#acceptAllButton";
    protected function _getOXID(): array
    {
        return [Module::PAYMENT_PAYPAL_ID];
    }

    protected function _getPaymentId(): string
    {
        return "payment_" . Module::PAYMENT_PAYPAL_ID;
    }

    private function waitForSpinnerDisappearance(): void
    {
        $I = $this->user;
        $I->waitForElementNotVisible($this->preloaderSpinner, 30);
        $I->waitForElementNotVisible($this->globalSpinner, 30);
        $I->waitForElementNotVisible($this->spinner, 30);
    }

    private function waitForPayPalPage(): PaypalCest
    {
        $I = $this->user;

        $I->waitForDocumentReadyState();
        $I->waitForElementNotVisible($this->spinner, 90);
        $I->wait(10);

        if ($I->seePageHasElement($this->loginSection)) {
            $I->retryClick('.loginRedirect a');
            $I->waitForDocumentReadyState();
            $this->waitForSpinnerDisappearance();
            $I->waitForElementNotVisible($this->loginSection);
        }

        return $this;
    }

    private function acceptAllPayPalCookies(): void
    {
        $I = $this->user;

        // In case we have cookie message, accept all cookies
        if ($I->seePageHasElement($this->acceptAllPaypalCookies)) {
            $I->retryClick($this->acceptAllPaypalCookies);
            $I->waitForElementNotVisible($this->acceptAllPaypalCookies);
        }
    }
    /**
     * @param AcceptanceTester $I
     * @return void
     * @group paypal
     */
    public function checkOrderPaypal(AcceptanceTester $I): void
    {
        $I->wantToTest(" Paypal (Adyen) on Frontend order");
        $this->user = $I;

         // init includes login to OXID shop using credentials from fixtures
        $this->_initializeTest();

        $orderPage = $this->_choosePayment();

        // nasty! Paypal button is within an iframe, the library can't find it...
        // we search for the container to click
        $paypalButtonContainer = '#' . Module::PAYMENT_PAYPAL_ID . '-container';
        $I->waitForElement($paypalButtonContainer);
        $I->click($paypalButtonContainer);

        // After clicking the Paypal button, a popup opens to authenticate the user. We need to switch the context.
        $I->switchToNextTab();

        // Paypal Testuser comes from environment
        $paypalUser = $_ENV['PAYPAL_USER'];
        $paypalPwd = $_ENV['PAYPAL_PWD'];
        if (empty($paypalUser) || empty($paypalPwd)) {
            throw new \Exception('Paypal credentials not found');
        }

        $this->waitForPayPalPage();
        $this->acceptAllPayPalCookies();

        // Enter the Paypal username and submit the form
        $I->waitForElement('#email', 60);
        $I->fillField('#email', $paypalUser);
        $I->submitForm('form[name="login"]', ['email' => $paypalUser]);

        $this->waitForPayPalPage();

        // Enter the Paypal password and submit the form
        $I->waitForElement('#password', 60);
        $I->fillField('#password', $paypalPwd);
        $I->submitForm('form[name="login"]', ['password' => $paypalPwd]);

        $this->waitForPayPalPage();

        // our user has pre-selected a payment method
        $I->waitForElement('#payment-submit-btn');
        $I->click('#payment-submit-btn');

        // Paypal popup will close after the payment, switch back to main window
        $I->switchToPreviousTab();
        $orderNumber = $this->_checkSuccessfulPayment();

        // Database updates
        $I->updateInDatabase(
            'oxorder',
            ['ADYENPSPREFERENCE' => $orderNumber],
            ['OXORDERNR' => $orderNumber]
        );

        // Check order history
        $orderHistoryPage = new UserOrderHistory($I);
        $I->amOnPage($orderHistoryPage->URL);

        $langCode = Registry::getLang()->getLanguageAbbr();
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_PAYMENT_NOTE")
            . ': ' . Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions'][$langCode]['desc']);
        $I->waitForText(Translator::translate("OSC_ADYEN_ACCOUNT_ORDER_REFERENCE_NOTE")
            . ': ' . $orderNumber);
    }
}
