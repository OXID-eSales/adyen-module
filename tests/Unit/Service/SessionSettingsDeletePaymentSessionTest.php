<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsDeletePaymentSessionTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deletePaymentSession
     */
    public function testDeletePaymentSession()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME,
            'pspReference'
        );
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_ORDER_REFERENCE,
            'orderReference'
        );
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME,
            'EUR'
        );
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME,
            12.23
        );

        $this->createSessionSettings()->deletePaymentSession();

        $this->assertNull($this->getValueFromSession(SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME));
        $this->assertNull($this->getValueFromSession(SessionSettings::ADYEN_SESSION_ORDER_REFERENCE));
        $this->assertNull($this->getValueFromSession(SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME));
        $this->assertNull($this->getValueFromSession(SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME));
    }
}
