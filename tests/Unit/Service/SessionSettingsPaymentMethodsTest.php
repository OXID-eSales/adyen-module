<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsPaymentMethodsTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::setPaymentMethods
     */
    public function testSet()
    {
        $paymentMethods = ['apple' => [], 'google' => ''];
        $this->createSessionSettings()->setPaymentMethods($paymentMethods);

        $this->assertEquals(
            $paymentMethods,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_PAYMENTMETHODS_NAME)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getPaymentMethods
     *
     * @dataProvider getTestDataForGet
     */
    public function testGet($paymentMethodsFromSession, $expectedPaymentMethods)
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_PAYMENTMETHODS_NAME,
            $paymentMethodsFromSession
        );

        $this->assertEquals(
            $expectedPaymentMethods,
            $this->createSessionSettings()->getPaymentMethods()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deletePaymentMethods
     */
    public function testDelete()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_PAYMENTMETHODS_NAME,
            'EUR'
        );

        $sessionSettings = $this->createSessionSettings();
        $sessionSettings->deletePaymentMethods();

        $this->assertEquals(
            null,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_PAYMENTMETHODS_NAME)
        );
    }

    public function getTestDataForGet(): array
    {
        return [
            [
                ['apple' => [], 'google' => ''],
                ['apple' => [], 'google' => ''],
            ],
            [
                null,
                [],
            ],
        ];
    }
}
