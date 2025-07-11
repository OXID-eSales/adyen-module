<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

class KlarnaSandboxPaymentPage extends Page
{
    private const BUY_BUTTON_SELECTOR = '#buy_button';
    private const BACK_BUTTON_SELECTOR = 'div[data-testid="summary-nav-bar"] button';
    private const CONFIRM_MOBILE_BUTTON_SELECTOR = '#onContinue';
    private const CONFIRM_CODE_INPUT_SELECTOR = '[name=otp_field]';
    private const PHONE_INPUT_SELECTOR = '#phone';
    private const THANK_YOU_PAGE_CONTAINER_SELECTOR = '#thankyouPage';

    public function clickBuyButtonAndConfirm(): void
    {
        $this->I->waitForElementVisible(self::PHONE_INPUT_SELECTOR, 30);
        $this->I->click(self::PHONE_INPUT_SELECTOR);
        $this->I->type('01771234567');

        $this->I->waitForElementVisible(self::CONFIRM_MOBILE_BUTTON_SELECTOR, 30);
        $this->I->click(self::CONFIRM_MOBILE_BUTTON_SELECTOR);

        $this->I->waitForElementVisible(self::CONFIRM_CODE_INPUT_SELECTOR, 30);
        $this->I->click(self::CONFIRM_CODE_INPUT_SELECTOR);
        $this->I->type('111111');

        $this->I->waitForElementVisible(self::BUY_BUTTON_SELECTOR, 30);
        $this->I->waitUntilDisabled(self::BUY_BUTTON_SELECTOR);
        $this->I->click(self::BUY_BUTTON_SELECTOR);

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
