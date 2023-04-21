<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Controller;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Price;
use OxidSolutionCatalysts\Adyen\Controller\AdyenJSController;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\Eshop\Application\Model\User;
use OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Core\Response;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use PHPUnit\Framework\TestCase;

class AdyenJSControllerPaymentsTest extends TestCase
{
    private static $postDataSuccess = [
        'paymentMethod' =>
            [
                'type' => 'paypal',
                'subtype' => 'sdk',
            ],
    ];

    private static $paymentResults = [
        'status' => 'authorized',
    ];

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\AdyenJSController::payments()
     */
    public function testPaymentsSetNotFoundBecauseOfNoAmount()
    {
        $basketAmountValue = .0;
        $sessionAmountValue = .0;
        $orderReference = 'orderReference';
        $pspReference = 'pspReference';
        $createdOrderReference = '';

        $controller = $this->createControllerMock(
            self::$postDataSuccess,
            $basketAmountValue,
            $sessionAmountValue,
            self::$paymentResults,
            $orderReference,
            $pspReference,
            $createdOrderReference
        );
        $controller->payments();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\AdyenJSController::payments()
     */
    public function testPaymentsSetNotFoundBecauseOfNoPaymentMethod()
    {
        $basketAmountValue = 12.0;
        $sessionAmountValue = 12.0;
        $orderReference = 'orderReference';
        $pspReference = 'pspReference';
        $createdOrderReference = '';

        $controller = $this->createControllerMock(
            [],
            $basketAmountValue,
            $sessionAmountValue,
            self::$paymentResults,
            $orderReference,
            $pspReference,
            $createdOrderReference
        );
        $controller->payments();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\AdyenJSController::payments()
     */
    public function testPaymentsSuccess()
    {
        $basketAmountValue = 12.0;
        $sessionAmountValue = 12.0;
        $orderReference = 'orderReference';
        $pspReference = 'pspReference';
        $createdOrderReference = '';

        $controller = $this->createControllerMock(
            self::$postDataSuccess,
            $basketAmountValue,
            $sessionAmountValue,
            self::$paymentResults,
            $orderReference,
            $pspReference,
            $createdOrderReference
        );
        $controller->payments();
    }

    private function createControllerMock(
        array $postData,
        float $basketAmountValue,
        float $sessionAmountValue,
        array $paymentResults,
        string $orderReference,
        string $pspReference,
        ?string $createdOrderReference
    ): AdyenJSController {

        $userMock = $this->createMock(User::class);
        $viewConfigMock = $this->createMock(ViewConfig::class);
        $noAmountOrPaymentMethodSet = !$basketAmountValue || !isset($postData['paymentMethod']);

        $controllerMock = $this->getMockBuilder(AdyenJSController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getJsonPostData', 'jsonToArray', 'getServiceFromContainer', 'getUser', 'getViewConfig'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('getJsonPostData')
            ->willReturn(json_encode($postData));

        $controllerMock->expects($this->once())
            ->method('jsonToArray')
            ->willReturn($postData);

        $controllerMock->expects(
            $noAmountOrPaymentMethodSet ? $this->never() : $this->once()
        )
            ->method('getUser')
            ->willReturn($userMock);

        $controllerMock->expects(
            $noAmountOrPaymentMethodSet ? $this->never() : $this->once()
        )
            ->method('getViewConfig')
            ->willReturn($viewConfigMock);

        $returnValueMap = [
            ResponseHandler::class => $this->createResponseHandlerMock($paymentResults, $noAmountOrPaymentMethodSet),
            SessionSettings::class => $this->createSessionSettingsMock(
                $orderReference,
                $pspReference,
                $basketAmountValue,
                $sessionAmountValue,
                $noAmountOrPaymentMethodSet,
                $createdOrderReference
            ),
            PaymentJSControllerService::class => $this->createPaymentJSControllerServiceMock(
                $pspReference,
                $orderReference,
                $noAmountOrPaymentMethodSet,
                $basketAmountValue
            ),
            Payment::class => $this->createPaymentServiceMock(
                $basketAmountValue,
                $orderReference,
                $postData,
                $userMock,
                $viewConfigMock,
                $paymentResults,
                $noAmountOrPaymentMethodSet
            ),
        ];

        $invokeCount = 3;
        if (!$noAmountOrPaymentMethodSet) {
            ++$invokeCount;
        }

        $controllerMock->expects($this->exactly($invokeCount))
            ->method('getServiceFromContainer')
            ->willReturnCallback(fn($argument) => $returnValueMap[$argument]);

        return $controllerMock;
    }

    private function createPaymentServiceMock(
        float $basketAmountValue,
        string $orderReference,
        array $postData,
        User $user,
        ViewConfig $viewConfig,
        array $paymentResults,
        bool $noAmountOrPaymentMethodSet
    ): Payment {
        $mock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['collectPayments', 'getPaymentResult'])
            ->getMock();

        $mock->expects($noAmountOrPaymentMethodSet ? $this->never() : $this->once())
            ->method('collectPayments')
            ->with($basketAmountValue, $orderReference, $postData, $user, $viewConfig);

        $mock->expects($noAmountOrPaymentMethodSet ? $this->never() : $this->once())
            ->method('getPaymentResult')
            ->willReturn($paymentResults);

        return $mock;
    }

    private function createPaymentJSControllerServiceMock(
        string $pspReference,
        string $orderReference,
        bool $noAmountOrPaymentMethodSet,
        float $basketAmountValue
    ): PaymentJSControllerService {
        $mock = $this->getMockBuilder(PaymentJSControllerService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['cancelPaymentIfNecessary', 'createOrderReference'])
            ->getMock();

        $mock->expects($noAmountOrPaymentMethodSet ? $this->never() : $this->once())
            ->method('cancelPaymentIfNecessary')
            ->with($pspReference, $basketAmountValue, $orderReference);

        $mock->expects($noAmountOrPaymentMethodSet ? $this->never() : $this->once())
            ->method('createOrderReference')
            ->with($orderReference, $basketAmountValue)
            ->willReturn($orderReference);

        return $mock;
    }

    private function createSessionSettingsMock(
        string $orderReference,
        string $pspReference,
        float $basketAmountValue,
        float $sessionAmountValue,
        bool $noAmountOrPaymentMethodSet,
        ?string $createdOrderReference
    ): SessionSettings {
        $mock = $this->getMockBuilder(SessionSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getOrderReference',
                    'getPspReference',
                    'getAmountValue',
                    'createOrderReference',
                    'setAmountValue',
                    'getBasket'
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('getOrderReference')
            ->willReturn($orderReference);

        $mock->expects($this->once())
            ->method('getPspReference')
            ->willReturn($pspReference);

        $mock->expects($this->once())
            ->method('getBasket')
            ->willReturn($this->createBasketMock($basketAmountValue));

        return $mock;
    }

    private function createBasketMock(float $basketGrossPrice): Basket
    {
        $mock = $this->getMockBuilder(Basket::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPrice'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getPrice')
            ->willReturn($this->createPriceMock($basketGrossPrice));

        return $mock;
    }

    private function createPriceMock(float $basketGrossPrice): Price
    {
        $mock = $this->getMockBuilder(Price::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBruttoPrice'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getBruttoPrice')
            ->willReturn($basketGrossPrice);

        return $mock;
    }

    private function createResponseHandlerMock(
        array $paymentResults,
        bool $noAmountOrPaymentMethodSet
    ): ResponseHandler {
        $mock = $this->getMockBuilder(ResponseHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['response'])
            ->getMock();

        $mock->expects($this->once())
            ->method('response')
            ->willReturn($this->createResponseMock($paymentResults, $noAmountOrPaymentMethodSet));

        return $mock;
    }

    private function createResponseMock(
        array $paymentResults,
        bool $noAmountOrPaymentMethodSet
    ): Response {
        $mock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setNotFound', 'setData', 'sendJson'])
            ->getMock();

        $mock->expects($noAmountOrPaymentMethodSet ? $this->once() : $this->never())
            ->method('setNotFound')
            ->willReturn($mock);

        $mock->expects($this->once())
            ->method('sendJson');

        $mock->expects($noAmountOrPaymentMethodSet ? $this->never() : $this->once())
            ->method('setData')
            ->with($paymentResults)
            ->willReturn($mock);

        return $mock;
    }
}
