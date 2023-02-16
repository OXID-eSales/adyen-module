<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook\Handler;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorisationHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancellationHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;

class AuthorisationHandlerTest extends UnitTestCase
{
    use HandlerTestMockFactoryTrait;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorisationHandler::additionalUpdates
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorisationHandler::getAdyenAction
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorisationHandler::getAdyenStatus
     */
    public function testAdditionalUpdates()
    {
        $amountValue = 1.23;
        $amountCurrency = 'EUR';
        $eventDate = '20230216';
        $orderId = 'orderId';
        $shopId = 1;
        $pspReference = 'pspReference';
        $parentPspReference = 'parentPspReference';

        $orderMock = $this->createOrderMock(
            $orderId,
            0,
            'OK',
            1,
            2
        );

        $eventMock = $this->createEventMock(
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference,
            2,
            2,
            2
        );

        /** @var AuthorisationHandler $handlerMock */
        $handlerMock = $this->createHandlerMock(
            AuthorisationHandler::class,
            $orderMock,
            $shopId,
            $orderId,
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference,
            Module::ADYEN_STATUS_AUTHORISED,
            Module::ADYEN_ACTION_AUTHORIZE,
            1,
            true,
            2
        );

        $handlerMock->setData($eventMock);
        $handlerMock->updateStatus($eventMock);
    }
}
