<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Service\OrderIsAdyenCapturePossibleService;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderIsAdyenCapturePossibleServiceTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OrderIsAdyenCapturePossibleService
     */
    public function testIsTrue(): void
    {
        $orderId = '12345';
        $orderIsAdyenCapturePossibleService = new OrderIsAdyenCapturePossibleService(
            $this->createOxNewServiceMock($orderId, Module::ADYEN_STATUS_AUTHORISED, 2)
        );

        $this->assertTrue(
            $orderIsAdyenCapturePossibleService->isAdyenCapturePossible($orderId)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OrderIsAdyenCapturePossibleService
     */
    public function testIsFalseBecauseOfReceived(): void
    {
        $orderId = '12345';
        $orderIsAdyenCapturePossibleService = new OrderIsAdyenCapturePossibleService(
            $this->createOxNewServiceMock($orderId, Module::ADYEN_STATUS_RECEIVED, 2)
        );

        $this->assertFalse(
            $orderIsAdyenCapturePossibleService->isAdyenCapturePossible($orderId, Module::ADYEN_STATUS_RECEIVED)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OrderIsAdyenCapturePossibleService
     */
    public function testIsFalseBecauseOfNoHistory(): void
    {
        $orderId = '12345';
        $orderIsAdyenCapturePossibleService = new OrderIsAdyenCapturePossibleService(
            $this->createOxNewServiceMock($orderId, Module::ADYEN_STATUS_RECEIVED, 0)
        );

        $this->assertFalse(
            $orderIsAdyenCapturePossibleService->isAdyenCapturePossible($orderId, Module::ADYEN_STATUS_RECEIVED)
        );
    }

    private function createOxNewServiceMock(string $orderId, string $status, int $historyCount): OxNewService
    {
        $oxNewServiceMock = $this->createMock(OxNewService::class);
        $oxNewServiceMock->expects($this->once())
            ->method('oxNew')
            ->with(AdyenHistoryList::class, [AdyenHistory::class])
            ->willReturn($this->createAdyenHistoryListMock($orderId, $status, $historyCount));

        return $oxNewServiceMock;
    }

    private function createAdyenHistoryListMock(string $orderId, string $status, int $historyCount): MockObject
    {
        $adyenHistoryListMock = $this->createMock(AdyenHistoryList::class);

        $adyenHistoryListMock->expects($this->once())
            ->method('getAdyenHistoryList')
            ->with($orderId, 'desc');

        $adyenHistoryListMock->expects($this->once())
            ->method('count')
            ->willReturn($historyCount);

        if ($historyCount > 0) {
            $invocationRule = $this->once();
        } else {
            $invocationRule = $this->never();
        }

        $adyenHistoryListMock->expects($invocationRule)
            ->method('getArray')
            ->willReturn($this->createAdyenHistoryArray($status));

        return $adyenHistoryListMock;
    }

    private function createAdyenHistoryArray(string $status): array
    {
        $adyenHistoryAuthorised = new AdyenHistory();
        $adyenHistoryAuthorised->setAdyenStatus(Module::ADYEN_STATUS_AUTHORISED);

        $adyenHistoryReceived = new AdyenHistory();
        $adyenHistoryReceived->setAdyenStatus(Module::ADYEN_STATUS_RECEIVED);

        if ($status === Module::ADYEN_STATUS_AUTHORISED) {
            return [
                $adyenHistoryAuthorised,
                $adyenHistoryReceived
            ];
        }

        return [
            $adyenHistoryReceived,
            $adyenHistoryAuthorised
        ];
    }
}
