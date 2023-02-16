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

class WebhookHandlerBaseHandleTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::handle
     */
    public function testHandleSuccess()
    {
        $eventMock = $this->createEventMock();
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $eventMock,
            1,
            1,
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->handle($eventMock);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::handle
     */
    public function testHandleFailureBecauseOfHmac()
    {
        $eventMock = $this->createEventMock(
            1,
            false,
            0,
            true,
            0,
            false
        );
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $eventMock,
            0,
            0
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->handle($eventMock);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::handle
     */
    public function testHandleFailureBecauseOfMerchant()
    {
        $eventMock = $this->createEventMock(
            1,
            true,
            1,
            false,
            0,
            false
        );
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $eventMock,
            0,
            0
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->handle($eventMock);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::handle
     */
    public function testHandleFailureBecauseOfEventNotSuccess()
    {
        $eventMock = $this->createEventMock(
            1,
            true,
            1,
            true,
            1,
            false
        );
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            $eventMock,
            0,
            0
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->handle($eventMock);
    }

    private function createWebHookHandlerBaseMock(
        MockObject $eventMock,
        int $setDataInvokeAmount = 1,
        int $updateStatusInvokeAmount = 1
    ): MockObject {
        $orderMock = $this->createOrderMock();
        $mockBuilder = $this->getMockBuilder(WebhookHandlerBase::class);
        $mockBuilder->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    $this->createPaymentMock(),
                    $orderMock,
                    $this->createAdyenHistoryListMock(),
                    $this->createContextMock()
                ]
            )->onlyMethods(['setData', 'updateStatus']);
        $webHookHandlerBaseMock = $mockBuilder->getMockForAbstractClass();

        $webHookHandlerBaseMock->expects($this->exactly($setDataInvokeAmount))
            ->method('setData')
            ->with($eventMock);
        $webHookHandlerBaseMock->expects($this->exactly($updateStatusInvokeAmount))
            ->method('updateStatus')
            ->with($eventMock);

        return $webHookHandlerBaseMock;
    }

    private function createContextMock(): MockObject
    {
        $contextMock = $this->createMock(Context::class);

        return $contextMock;
    }

    private function createPaymentMock(): MockObject
    {
        $paymentMock = $this->createMock(Payment::class);

        return $paymentMock;
    }

    private function createEventMock(
        int $isHMACVerifiedInvokeAmount = 1,
        bool $isHMACVerified = true,
        int $isMerchantVerifiedInvokeAmount = 1,
        bool $isMerchantVerified = true,
        int $isSuccessInvokeAmount = 1,
        bool $isSuccess = true
    ) {
        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly($isHMACVerifiedInvokeAmount))
            ->method('isHMACVerified')
            ->willReturn($isHMACVerified);
        $eventMock->expects($this->exactly($isMerchantVerifiedInvokeAmount))
            ->method('isMerchantVerified')
            ->willReturn($isMerchantVerified);
        $eventMock->expects($this->exactly($isSuccessInvokeAmount))
            ->method('isSuccess')
            ->willReturn($isSuccess);

        return $eventMock;
    }

    private function createOrderMock(): MockObject
    {
        $orderMock = $this->createMock(Order::class);

        return $orderMock;
    }

    private function createAdyenHistoryListMock(): MockObject
    {
        $adyenHistoryListMock = $this->createMock(AdyenHistoryList::class);

        return $adyenHistoryListMock;
    }
}
