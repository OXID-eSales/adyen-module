<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

use OxidSolutionCatalysts\Adyen\Core\Module;

class CheckoutAdyenPage extends Page
{
    private const PAY_BUTTON_CONTAINER_SELECTOR = '#*-container';

    public function clickAdyenPayButton(string $paymentId)
    {
        $selector = $this->getPaymentButtonContainerSelector($paymentId);
        $this->I->waitForElement($selector);
        $this->I->click($selector);
    }

    private function getPaymentButtonContainerSelector(string $paymentId = Module::PAYMENT_PAYPAL_ID)
    {
        return str_replace('*', $paymentId, self::PAY_BUTTON_CONTAINER_SELECTOR);
    }
}
