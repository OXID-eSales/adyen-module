<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsAmountCurrencyTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::setAmountCurrency
     */
    public function testSet()
    {
        $amountCurrency = 'EUR';
        $this->createSessionSettings()->setAmountCurrency($amountCurrency);

        $this->assertEquals(
            $amountCurrency,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getAmountCurrency
     *
     * @dataProvider getTestDataForGet
     */
    public function testGet($amountCurrencyFromSession, $expectedAmountCurrency)
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME,
            $amountCurrencyFromSession
        );

        $this->assertEquals(
            $expectedAmountCurrency,
            $this->createSessionSettings()->getAmountCurrency()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deleteAmountCurrency
     */
    public function testDelete()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME,
            'EUR'
        );

        $sessionSettings = $this->createSessionSettings();
        $sessionSettings->deleteAmountCurrency();

        $this->assertEquals(
            null,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME)
        );
    }

    public function getTestDataForGet(): array
    {
        return [
            [
                'EUR',
                'EUR',
            ],
            [
                null,
                ''
            ],
        ];
    }
}
