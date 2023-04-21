<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

class TwintSandboxPaymentPage extends Page
{
    public const RESULT_AUTHORISED = 'authorised';
    public const RESULT_CANCELLED = 'cancelled';
    public const RESULT_ERROR = 'error';
    public const RESULT_REFUSED = 'refused';
    private const RESULT_FORM_SELECTOR = '#simulatePaymentFormId';
    private const RESULT_BUTTON_SELECTOR = self::RESULT_FORM_SELECTOR . ' button[value="*"]';

    public function clickTwintSandboxButton(string $result = self::RESULT_AUTHORISED): void
    {
        $buttonSelector = $this->getSelectorForButton($result);
        $this->I->waitForElement($buttonSelector, 30);
        $this->I->click($buttonSelector);
    }

    private function getSelectorForButton(string $result): string
    {
        return str_replace('*', $result, self::RESULT_BUTTON_SELECTOR);
    }
}
