<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsAmountValueTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::setAmountValue
     */
    public function testSet()
    {
        $amountValue = 12.23;
        $this->createSessionSettings()->setAmountValue($amountValue);

        $this->assertEquals(
            $amountValue,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getAmountValue
     *
     * @dataProvider getTestDataForGet
     */
    public function testGet($amountValueFromSession, $expectedAmountValue)
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME,
            $amountValueFromSession
        );

        $this->assertEquals(
            $expectedAmountValue,
            $this->createSessionSettings()->getAmountValue()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deleteAmountValue
     */
    public function testDelete()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME,
            12.23
        );

        $sessionSettings = $this->createSessionSettings();
        $sessionSettings->deleteAmountValue();

        $this->assertEquals(
            null,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME)
        );
    }

    public function getTestDataForGet(): array
    {
        return [
            [
                12.23,
                12.23,
            ],
            [
                null,
                0.0
            ],
        ];
    }
}
