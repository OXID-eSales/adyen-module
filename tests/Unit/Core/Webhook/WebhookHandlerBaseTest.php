<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook;

use Adyen\Util\HmacSignature;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;

class WebhookHandlerBaseTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\WebhookHandlerBase::handle
     */
    public function testHandle()
    {
        $event = $this->createEvent();
        $webHookHandlerBaseMock = $this->createWebHookHandlerBaseMock(
            1,
            'paymentId',
            1,
            $event->getPspReference()
        );

        /** @var WebhookHandlerBase $webHookHandlerBaseMock */
        $webHookHandlerBaseMock->handle($event);
    }

    private function createWebHookHandlerBaseMock(
        int $paymentLoadInvokeAmount = 1,
        string $paymentId = 'paymentId',
        int $getOxidOrderIdByPSPReferenceInvokeAmount = 1,
        string $pspReference = 'pspReference',
        string $orderId = 'orderId',
        int $loadInvokeAmount = 1,
        int $getAdyenStringDataInvokeAmount = 1,
        int $getShopInvokeAmount = 1,
        int $shopId = 1,
        int $getOrderIdInvokeAmount = 1
    ): MockObject {
        $orderMock = $this->createOrderMock(
            $loadInvokeAmount,
            $orderId,
            $getAdyenStringDataInvokeAmount,
            $paymentId,
            $getOrderIdInvokeAmount
        );
        $mockBuilder = $this->getMockBuilder(WebhookHandlerBase::class);
        $mockBuilder->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    $this->createPaymentMock($paymentLoadInvokeAmount, $paymentId),
                    $orderMock,
                    $this->createAdyenHistoryListMock(
                        $getOxidOrderIdByPSPReferenceInvokeAmount,
                        $pspReference,
                        $orderId
                    ),
                    $this->createContextMock($getShopInvokeAmount, $shopId)
                ]
            );
        $webHookHandlerBaseMock = $mockBuilder->getMockForAbstractClass();

        return $webHookHandlerBaseMock;
    }

    private function createContextMock(
        int $getShopInvokeAmount,
        int $shopId
    ): MockObject {
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->exactly($getShopInvokeAmount))
            ->method('getCurrentShopId')
            ->willReturn($shopId);

        return $contextMock;
    }

    private function createPaymentMock(
        int $paymentLoadInvokeAmount = 1,
        string $paymentId = 'paymentId'
    ): MockObject {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->expects($this->exactly($paymentLoadInvokeAmount))
            ->method('load')
            ->with($paymentId);

        return $paymentMock;
    }

    private function createEvent()
    {
        return \oxNew(Event::class, $this->createEventRawData());
    }

    private function createOrderMock(
        int $loadInvokeAmount,
        string $orderId,
        int $getAdyenStringDataInvokeAmount,
        string $paymentId,
        int $getIdInvokeAmount
    ): MockObject {
        $orderMock = $this->createMock(Order::class);

        $orderMock->expects($this->exactly($loadInvokeAmount))
            ->method('load')
            ->with($orderId)
            ->willReturn($orderMock);

        $orderMock->expects($this->exactly($loadInvokeAmount))
            ->method('getAdyenStringData')
            ->willReturn($paymentId);

        $orderMock->expects($this->exactly($getIdInvokeAmount))
            ->method('getId')
            ->willReturn($orderId);

        return $orderMock;
    }

    private function createAdyenHistoryListMock(
        int $getOxidOrderIdByPSPReferenceInvokeAmount,
        string $pspReference,
        string $orderId
    ): MockObject {
        $adyenHistoryListMock = $this->createMock(AdyenHistoryList::class);
        $adyenHistoryListMock->expects($this->exactly($getOxidOrderIdByPSPReferenceInvokeAmount))
            ->method('getOxidOrderIdByPSPReference')
            ->with($pspReference)
            ->willReturn($orderId);

        return $adyenHistoryListMock;
    }

    private function createEventRawData(
        bool $isValidHmac = true,
        string $merchantAccountCode = 'SandboxMerchantAccount',
        bool $success = true
    ): array {
        return [
            "live" => "false",
            "notificationItems" => [
                [
                    "NotificationRequestItem" => [
                        "additionalData" => [
                            "hmacSignature" => 'dummyHmac',
                        ],
                        "amount" => [
                            "currency" => "EUR",
                            "value" => 1000
                        ],
                        "eventDate" => "2021-01-01T01:00:00+01:00",
                        "pspReference" => "9313547924770610",
                        "originalReference" => "1233547924770610",
                        "eventCode" => "AUTHORIZATION",
                        "merchantReference" => "TestMerchantReference",
                        "success" => "true"
                    ]
                ]
            ],
            'hmacSignatureUtil' => $this->createHmacSignatureUtilMock($isValidHmac),
            'merchantAccountCode' => $merchantAccountCode,
            'success' => $success,
        ];
    }

    private function createHmacSignatureUtilMock(bool $isValidHmac = true): MockObject
    {
        $hmacSignatureMock = $this->createMock(HmacSignature::class);

        return $hmacSignatureMock;
    }
}
