<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Codeception\Acceptance\Page;

use OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page\Page;

class KlarnaSandboxPaymentPage extends Page
{
    private const IFRAME_FULLSCREEN_SELECTOR = '#klarna-hpp-instance-fullscreen';
    private const BUY_BUTTON_SELECTOR = '#buy-button';
    private const BACK_BUTTON_SELECTOR = '#back-button';
    private const CONFIRM_MOBILE_BUTTON_SELECTOR = '#onContinue';
    private const CONFIRM_CODE_INPUT_SELECTOR = '[name=otp_field]';
    private const PHONE_INPUT_SELECTOR = '#email_or_phone';
    private const CONFIRM_PAYMENT_BUTTON_SELECTOR = '#invoice_kp-purchase-review-continue-button';
    private const THANK_YOU_PAGE_CONTAINER_SELECTOR = '#thankyouPage';

    public function clickBuyButtonAndConfirm(): void
    {
        $this->I->waitForPageLoad(30);
        $this->I->waitForElementVisible(self::BUY_BUTTON_SELECTOR, 30);
        $this->I->waitUntilDisabled(self::BUY_BUTTON_SELECTOR);
        $this->I->click(self::BUY_BUTTON_SELECTOR);

        $this->I->waitForElementVisible(self::IFRAME_FULLSCREEN_SELECTOR, 30);
        $this->I->switchToIFrame(self::IFRAME_FULLSCREEN_SELECTOR);

        $this->I->waitForElementVisible(self::PHONE_INPUT_SELECTOR, 30);
        $this->I->click(self::PHONE_INPUT_SELECTOR);
        $this->I->type('01771234567');

        $this->I->waitForElementVisible(self::CONFIRM_MOBILE_BUTTON_SELECTOR, 30);
        $this->I->click(self::CONFIRM_MOBILE_BUTTON_SELECTOR);

        $this->I->waitForElementVisible(self::CONFIRM_CODE_INPUT_SELECTOR, 30);
        $this->I->click(self::CONFIRM_CODE_INPUT_SELECTOR);
        $this->I->type('111111');

        $this->I->waitForElementVisible(self::CONFIRM_PAYMENT_BUTTON_SELECTOR, 30);
        $this->I->waitUntilDisabled(self::CONFIRM_PAYMENT_BUTTON_SELECTOR);
        $this->I->click(self::CONFIRM_PAYMENT_BUTTON_SELECTOR);

        $this->I->waitForElementVisible(self::THANK_YOU_PAGE_CONTAINER_SELECTOR, 30);
    }

    public function clickBuyButtonAndCancel(): void
    {
        $this->I->waitForPageLoad(30);
        $this->I->waitForElementVisible(self::BACK_BUTTON_SELECTOR, 30);
        $this->I->waitUntilDisabled(self::BACK_BUTTON_SELECTOR);
        $this->I->click(self::BACK_BUTTON_SELECTOR);
    }
}
