<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsResultCodeTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::setResultCode
     */
    public function testSet()
    {
        $resultCode = 'EUR';
        $this->createSessionSettings()->setResultCode($resultCode);

        $this->assertEquals(
            $resultCode,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_RESULTCODE_NAME)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getResultCode
     *
     * @dataProvider getTestDataForGet
     */
    public function testGet($resultCodeFromSession, $expectedResultCode)
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_RESULTCODE_NAME,
            $resultCodeFromSession
        );

        $this->assertEquals(
            $expectedResultCode,
            $this->createSessionSettings()->getResultCode()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deleteResultCode
     */
    public function testDelete()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_RESULTCODE_NAME,
            'EUR'
        );

        $sessionSettings = $this->createSessionSettings();
        $sessionSettings->deleteResultCode();

        $this->assertEquals(
            null,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_RESULTCODE_NAME)
        );
    }

    public function getTestDataForGet(): array
    {
        return [
            [
                'resultCode',
                'resultCode',
            ],
            [
                null,
                ''
            ],
        ];
    }
}
