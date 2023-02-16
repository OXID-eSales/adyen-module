<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;

class AdyenHistoryGetCanceledSumTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getCanceledSum
     */
    public function testLoadByPSPReference()
    {
        $pspReference = 'pspReference';
        $returnValue = 1.2;

        $builder = $this->getMockBuilder(AdyenHistory::class);
        $builder->onlyMethods(['getSumByAction']);
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('getSumByAction')
            ->with(
                $pspReference,
                Module::ADYEN_ACTION_CANCEL,
                Module::ADYEN_STATUS_CANCELLED
            )
            ->willReturn($returnValue);

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getCanceledSum($pspReference)
        );
    }
}
