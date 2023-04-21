<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsPspReferenceTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::setPspReference
     */
    public function testSet()
    {
        $pspReference = 'EUR';
        $this->createSessionSettings()->setPspReference($pspReference);

        $this->assertEquals(
            $pspReference,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getPspReference
     *
     * @dataProvider getTestDataForGet
     */
    public function testGet($pspReferenceFromSession, $expectedPspReference)
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME,
            $pspReferenceFromSession
        );

        $this->assertEquals(
            $expectedPspReference,
            $this->createSessionSettings()->getPspReference()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deletePspReference
     */
    public function testDelete()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME,
            'EUR'
        );

        $sessionSettings = $this->createSessionSettings();
        $sessionSettings->deletePspReference();

        $this->assertEquals(
            null,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME)
        );
    }

    public function getTestDataForGet(): array
    {
        return [
            [
                'pspReference',
                'pspReference',
            ],
            [
                null,
                ''
            ],
        ];
    }
}
