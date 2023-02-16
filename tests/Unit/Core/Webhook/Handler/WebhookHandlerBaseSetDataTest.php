<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook\Handler;

use Exception;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;

class WebhookHandlerBaseSetDataTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::setData
     */
    public function testSetDataSuccess()
    {
        $pspReference = 'pspReference';
        $parentPspReference = 'parentPspReference';
        $paymentId = 'paymentId';
        $eventMock = $this->createEventMock(
            $pspReference,
            $parentPspReference
        );
        $orderMock = $this->createOrderMock(
            $paymentId
        );
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $paymentId,
            1,
            $pspReference,
            1,
            $orderMock
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->setData($eventMock);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::setData
     */
    public function testSetDataException()
    {
        $pspReference = 'pspReference';
        $parentPspReference = 'parentPspReference';
        $paymentId = null;
        $eventMock = $this->createEventMock(
            $pspReference,
            $parentPspReference
        );
        // getOrderByAdyenPSPReference returns no order
        $orderMock = null;
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $paymentId,
            0,
            $pspReference,
            1,
            $orderMock
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("order not found by psp reference {$pspReference}");
        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->setData($eventMock);
    }

    private function createWebHookHandlerBaseMock(
        ?string $paymentId,
        int $paymentLoadInvokeAmount,
        string $pspReference,
        int $shopId,
        ?MockObject $order
    ): MockObject {
        $mockBuilder = $this->getMockBuilder(WebhookHandlerBase::class);
        $mockBuilder->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    $this->createPaymentMock($paymentId, $paymentLoadInvokeAmount),
                    $order,
                    $this->createAdyenHistoryListMock(),
                    $this->createContextMock($shopId)
                ]
            )->onlyMethods(['getOrderByAdyenPSPReference']);
        $webHookHandlerBaseMock = $mockBuilder->getMockForAbstractClass();

        $webHookHandlerBaseMock->expects($this->once())
            ->method('getOrderByAdyenPSPReference')
            ->with($pspReference)
            ->willReturn($order);

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

    private function createPaymentMock(
        ?string $paymentId,
        int $paymentLoadInvokeAmount
    ): MockObject {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->expects($this->exactly($paymentLoadInvokeAmount))
            ->method('load')
            ->with($paymentId);

        return $paymentMock;
    }

    private function createEventMock(
        string $pspReference,
        string $parentPspReference
    ) {
        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getPspReference')
            ->willReturn($pspReference);
        $eventMock->expects($this->exactly(2))
            ->method('getParentPspReference')
            ->willReturn($parentPspReference);

        return $eventMock;
    }

    private function createOrderMock(?string $paymentId): MockObject
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->once())
            ->method('getAdyenStringData')
            ->with('oxpaymenttype')
            ->willReturn($paymentId);

        return $orderMock;
    }

    private function createAdyenHistoryListMock(): MockObject
    {
        $adyenHistoryListMock = $this->createMock(AdyenHistoryList::class);

        return $adyenHistoryListMock;
    }
}
