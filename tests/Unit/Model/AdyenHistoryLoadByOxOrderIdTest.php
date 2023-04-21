<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;

class AdyenHistoryLoadByOxOrderIdTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::loadByOxOrderId
     */
    public function testLoadByPSPReference()
    {
        $field = 'oxorderid';
        $oxOrderId = 'order12';
        $returnValue = true;

        $builder = $this->getMockBuilder(AdyenHistory::class);
        $builder->onlyMethods(['loadByIdent']);
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('loadByIdent')
            ->with(
                $field,
                $oxOrderId
            )
            ->willReturn($returnValue);

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->loadByOxOrderId($oxOrderId)
        );
    }
}
