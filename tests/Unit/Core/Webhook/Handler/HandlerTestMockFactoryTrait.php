<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;
use OxidSolutionCatalysts\Adyen\Core\Module;

trait HandlerTestMockFactoryTrait
{
    private function createOrderMock(
        string $orderId,
        int $setAdyenOrderStatusInvokeAmount = 0,
        string $adyenOrderStatus = 'OK',
        int $markAdyenOrderAsPaidInvokeAmount = 0,
        int $orderGetIdInvokeAmount = 1
    ): MockObject {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->exactly($orderGetIdInvokeAmount))
            ->method('getId')
            ->willReturn($orderId);
        $orderMock->expects($this->exactly($setAdyenOrderStatusInvokeAmount))
            ->method('setAdyenOrderStatus')
            ->with($adyenOrderStatus);
        $orderMock->expects($this->exactly($markAdyenOrderAsPaidInvokeAmount))
            ->method('markAdyenOrderAsPaid');

        return $orderMock;
    }

    private function createEventMock(
        float $amountValue,
        string $amountCurrency,
        string $eventDate,
        string $pspReference,
        string $parentPspReference,
        int $getAmountValueInvokeAmount = 1,
        int $getAmountCurrencyInvokeAmount = 1,
        int $getEventDateInvokeAmount = 1
    ) {
        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly($getAmountValueInvokeAmount))
            ->method('getAmountValue')
            ->willReturn($amountValue);
        $eventMock->expects($this->exactly($getAmountCurrencyInvokeAmount))
            ->method('getAmountCurrency')
            ->willReturn($amountCurrency);
        $eventMock->expects($this->exactly($getEventDateInvokeAmount))
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

    private function createContextMock(int $shopId): MockObject
    {
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->once())
            ->method('getCurrentShopId')
            ->willReturn($shopId);

        return $contextMock;
    }

    private function createHandlerMock(
        string $handlerClass,
        MockObject $orderMock,
        int $shopId,
        string $orderId,
        string $amountValue,
        string $amountCurrency,
        string $eventDate,
        string $pspReference,
        string $parentPspReference,
        string $adyenStatus,
        string $adyenAction,
        int $isAdyenImmediateCaptureInvokeAmount = 0,
        bool $isCapture = false,
        int $setHistoryEntryInvokeAmount = 1
    ): MockObject {
        $builder = $this->getMockBuilder($handlerClass);
        $builder->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    $this->createPaymentMock(
                        $isAdyenImmediateCaptureInvokeAmount,
                        $isCapture
                    ),
                    $orderMock,
                    null,
                    $this->createContextMock($shopId)
                ]
            );
        $builder->onlyMethods(
            ['setHistoryEntry', 'getOrderByAdyenPSPReference']
        );
        $handlerMock = $builder->getMock();
        $handlerMock->expects($this->exactly($setHistoryEntryInvokeAmount))
            ->method('setHistoryEntry')
            ->withConsecutive(
                [
                    $orderId,
                    $shopId,
                    $amountValue,
                    $amountCurrency,
                    $eventDate,
                    $pspReference,
                    $parentPspReference,
                    $adyenStatus,
                    $adyenAction
                ],
                [
                    $orderId,
                    $shopId,
                    $amountValue,
                    $amountCurrency,
                    $eventDate,
                    $pspReference,
                    $parentPspReference,
                    Module::ADYEN_STATUS_CAPTURED,
                    Module::ADYEN_ACTION_CAPTURE
                ]
            );
        $handlerMock->expects($this->once())
            ->method('getOrderByAdyenPSPReference')
            ->willReturn($orderMock);

        return $handlerMock;
    }

    private function createPaymentMock(
        int $isAdyenImmediateCaptureInvokeAmount,
        bool $isCapture
    ): MockObject {
        $builder = $this->getMockBuilder(Payment::class);
        $builder->onlyMethods(['isAdyenImmediateCapture']);
        $paymentMock = $builder->getMock();
        $paymentMock->expects($this->exactly($isAdyenImmediateCaptureInvokeAmount))
            ->method('isAdyenImmediateCapture')
            ->willReturn($isCapture);

        return $paymentMock;
    }
}
