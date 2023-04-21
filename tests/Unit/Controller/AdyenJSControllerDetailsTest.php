<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Controller;

use OxidSolutionCatalysts\Adyen\Controller\AdyenJSController;
use OxidSolutionCatalysts\Adyen\Service\PaymentDetails;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Core\Response;
use PHPUnit\Framework\TestCase;

class AdyenJSControllerDetailsTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\AdyenJSController::details
     * @dataProvider getTestData
     */
    public function testDetails(
        array $postData,
        array $paymentDetails
    ) {
        $this->createControllerMock(
            $postData,
            $paymentDetails
        )->details();
    }
    private function createControllerMock(
        array $postData,
        array $paymentDetails
    ): AdyenJSController {
        $controllerMock = $this->getMockBuilder(AdyenJSController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServiceFromContainer', 'jsonToArray', 'getJsonPostData'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('getJsonPostData')
            ->willReturn(json_encode($postData));

        $controllerMock->expects($this->once())
            ->method('jsonToArray')
            ->willReturn($postData);

        $invokeCount = isset($postData['details']) ? 2 : 1;
        $returnValueMap = [
            ResponseHandler::class => $this->createResponseHandlerMock($postData, $paymentDetails),
            PaymentDetails::class => $this->createPaymentDetailsMock($postData),
        ];

        $controllerMock->expects($this->exactly($invokeCount))
            ->method('getServiceFromContainer')
            ->willReturnCallback(fn($argument) => $returnValueMap[$argument]);

        return $controllerMock;
    }

    public function getTestData(): array
    {
        return [
            [
                [],
                []
            ],
            [
                ['details' => 'details'],
                []
            ],
        ];
    }

    private function createPaymentDetailsMock(
        array $postData
    ): PaymentDetails {
        $mock = $this->getMockBuilder(PaymentDetails::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['collectPaymentDetails'])
            ->getMock();

        $mock->expects(isset($postData['details']) ? $this->once() : $this->never())
            ->method('collectPaymentDetails')
            ->with($postData);

        return $mock;
    }

    private function createResponseHandlerMock(
        array $postData,
        array $paymentDetails
    ): ResponseHandler {
        $responseHandlerMock = $this->getMockBuilder(ResponseHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['response'])
            ->getMock();

        $responseHandlerMock->expects($this->once())
            ->method('response')
            ->willReturn($this->createResponseMock($postData, $paymentDetails));

        return $responseHandlerMock;
    }

    private function createResponseMock(
        array $postData,
        array $paymentDetails
    ): Response {
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['sendJson', 'setNotFound', 'setData'])
            ->getMock();
        $responseMock->expects($this->once())
            ->method('sendJson');
        $responseMock->expects(!isset($postData['details']) ? $this->once() : $this->never())
            ->method('setNotFound')
            ->willReturn($responseMock);

        $responseMock->expects(isset($postData['details']) ? $this->once() : $this->never())
            ->method('setData')
            ->with($paymentDetails)
            ->willReturn($responseMock);

        return $responseMock;
    }
}
