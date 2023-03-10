<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook\Handler;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;

class WebhookHandlerBaseUpdateStatusTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::updateStatus
     */
    public function testUpdateStatus()
    {
        $pspReference = 'pspReference';
        $parentPspReference = 'parentPspReference';
        $amountValue = 1.23;
        $amountCurrency = 'EUR';
        $eventDate = '20230216';
        $adyenStatus = 'adyenStatus';
        $adyenAction = 'adyenAction';
        $eventMock = $this->createEventMock(
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference
        );
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $eventMock,
            'orderId',
            $pspReference,
            $parentPspReference,
            1,
            $amountValue,
            $amountCurrency,
            $adyenStatus,
            $adyenAction,
            $eventDate
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        // we need to call setData before updateStatus because of
        // source/source/modules/osc/adyen/src/Core/Webhook/Handler/WebhookHandlerBase.php:93
        $webHookHandlerBaseMock->setData($eventMock);
        $webHookHandlerBaseMock->updateStatus($eventMock);
    }

    private function createWebHookHandlerBaseMock(
        MockObject $eventMock,
        string $orderId,
        string $pspReference,
        string $parentPspReference,
        int $shopId,
        float $amountValue,
        string $amountCurrency,
        string $adyenStatus,
        string $adyenAction,
        string $eventDate
    ): MockObject {
        $orderMock = $this->createOrderMock(
            $orderId
        );
        $mockBuilder = $this->getMockBuilder(WebhookHandlerBase::class);
        $mockBuilder->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    $this->createPaymentMock(),
                    $orderMock,
                    $this->createAdyenHistoryListMock(),
                    $this->createContextMock($shopId)
                ]
            )->onlyMethods(
                [
                    'additionalUpdates',
                    'setHistoryEntry',
                    'getOrderByAdyenPSPReference',
                    'getAdyenStatus',
                    'getAdyenAction'
                ]
            );
        $webHookHandlerBaseMock = $mockBuilder->getMockForAbstractClass();

        $webHookHandlerBaseMock->expects($this->once())
            ->method('additionalUpdates')
            ->with($eventMock);
        $webHookHandlerBaseMock->expects($this->once())
            ->method('setHistoryEntry')
            ->with(
                $orderId,
                $shopId,
                $amountValue,
                $amountCurrency,
                $eventDate,
                $pspReference,
                $parentPspReference,
                $adyenStatus,
                $adyenAction
            );
        $webHookHandlerBaseMock->expects($this->once())
            ->method('getOrderByAdyenPSPReference')
            ->with($pspReference)
            ->willReturn($orderMock);
        $webHookHandlerBaseMock->expects($this->once())
            ->method('getAdyenStatus')
            ->willReturn($adyenStatus);
        $webHookHandlerBaseMock->expects($this->once())
            ->method('getAdyenAction')
            ->willReturn($adyenAction);

        return $webHookHandlerBaseMock;
    }

    private function createContextMock(int $shopId): MockObject
    {
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->once())
            ->method('getCurrentShopId')
            ->willReturn($shopId);

        return $contextMock;
    }

    private function createPaymentMock(): MockObject
    {
        $paymentMock = $this->createMock(Payment::class);

        return $paymentMock;
    }

    private function createEventMock(
        float $amountValue,
        string $amountCurrency,
        string $eventDate,
        string $pspReference,
        string $parentPspReference
    ) {
        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getAmountValue')
            ->willReturn($amountValue);
        $eventMock->expects($this->once())
            ->method('getAmountCurrency')
            ->willReturn($amountCurrency);
        $eventMock->expects($this->once())
            ->method('getEventDate')
            ->willReturn($eventDate);
        $eventMock->expects($this->once())
            ->method('getPspReference')
            ->willReturn($pspReference);
        $eventMock->expects($this->exactly(2))
            ->method('getParentPspReference')
            ->willReturn($parentPspReference);

        return $eventMock;
    }

    private function createOrderMock(string $orderId): MockObject
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->once())
            ->method('getId')
            ->willReturn($orderId);

        return $orderMock;
    }

    private function createAdyenHistoryListMock(): MockObject
    {
        $adyenHistoryListMock = $this->createMock(AdyenHistoryList::class);

        return $adyenHistoryListMock;
    }
}
