<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsOrderReferenceTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::createOrderReference
     */
    public function testCreate()
    {
        $orderReference = $this->createSessionSettings()->createOrderReference();
        $this->assertNotEmpty($orderReference);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getOrderReference
     *
     * @dataProvider getTestDataForGet
     */
    public function testGet($orderReferenceFromSession, $expectedOrderReference)
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_ORDER_REFERENCE,
            $orderReferenceFromSession
        );

        $this->assertEquals(
            $expectedOrderReference,
            $this->createSessionSettings()->getOrderReference()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::deleteOrderReference
     */
    public function testRemove()
    {
        $this->setValueInSession(
            SessionSettings::ADYEN_SESSION_ORDER_REFERENCE,
            'orderReference'
        );

        $sessionSettings = $this->createSessionSettings();
        $sessionSettings->deleteOrderReference();

        $this->assertEquals(
            null,
            $this->getValueFromSession(SessionSettings::ADYEN_SESSION_ORDER_REFERENCE)
        );
    }

    public function getTestDataForGet(): array
    {
        return [
            [
                'orderReference',
                'orderReference',
            ],
            [
                null,
                ''
            ],
        ];
    }
}
