<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use PHPUnit\Framework\MockObject\MockObject;

class AdyenHistoryDeleteTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::delete
     */
    public function testDeleteLoad()
    {
        $pspReference = 'pspReference';
        $oxid = 'oxid';

        $adyenHistoryMock = $this->createAdyenHistoryMock(
            $oxid,
            1,
            $pspReference
        );

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock->delete($oxid);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::delete
     */
    public function testDeleteNoLoad()
    {
        $pspReference = 'pspReference';
        $oxid = null;

        $adyenHistoryMock = $this->createAdyenHistoryMock(
            $oxid,
            0,
            $pspReference
        );

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock->delete($oxid);
    }

    private function createAdyenHistoryMock(
        ?string $oxid,
        int $loadInvokeAmount,
        string $pspReference
    ): MockObject {
        $builder = $this->getMockBuilder(AdyenHistory::class);
        $builder->onlyMethods(['load', 'getPSPReference', 'deleteChildReferences']);

        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->exactly($loadInvokeAmount))
            ->method('load')
            ->with($oxid);
        $adyenHistoryMock->expects($this->once())
            ->method('getPSPReference')
            ->willReturn($pspReference);
        $adyenHistoryMock->expects($this->once())
            ->method('deleteChildReferences')
            ->with($pspReference);

        return $adyenHistoryMock;
    }
}
