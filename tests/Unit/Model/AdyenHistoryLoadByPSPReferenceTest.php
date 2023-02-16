<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;

class AdyenHistoryLoadByPSPReferenceTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::loadByPSPReference
     */
    public function testLoadByPSPReference()
    {
        $pspReference = 'pspReferenceValue';
        /** AdyenHistory::PSPREFERENCEFIELD is protected, so we reference the same string here */
        $pspReferenceField = 'pspreference';
        $returnValue = true;

        $builder = $this->getMockBuilder(AdyenHistory::class);
        $builder->onlyMethods(['loadByIdent']);
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('loadByIdent')
            ->with(
                $pspReferenceField,
                $pspReference
            )
            ->willReturn($returnValue);

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->loadByPSPReference($pspReference)
        );
    }
}
