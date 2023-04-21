<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

use OxidSolutionCatalysts\Adyen\Core\Module;

class AdyenNotSuccessfulPage extends Page
{
    private const ERROR_MESSAGE_SELECTOR = '[data-adyen-return-code="*"]';

    public function checkMessage(string $resultCode = Module::ADYEN_RETURN_RESULT_CODE_CANCELLED): void
    {
        $this->I->waitForElement($this->getSelectorForMessage($resultCode));
    }

    private function getSelectorForMessage(string $resultCode): string
    {
        return str_replace('*', $resultCode, self::ERROR_MESSAGE_SELECTOR);
    }
}
