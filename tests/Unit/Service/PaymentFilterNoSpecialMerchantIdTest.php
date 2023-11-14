<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePayments;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Service\UserAddress;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Core\Module;
use PHPUnit\Framework\TestCase;
use OxidSolutionCatalysts\Adyen\Model\Payment as PaymentModel;

class PaymentFilterNoSpecialMerchantIdTest extends TestCase
{
    use ServiceContainer;



    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Payment::filterNoSpecialMerchantId()
     * @dataProvider getTestData
     */
    public function testFilterNoSpecialMerchantId(string $paymentId, string $merchantId, int $expectedPaymentsCount)
    {
        $paymentModels = [$this->createPaymentModelMock($paymentId)];
        $paymentService = $this->createPaymentMock($paymentModels[0], $merchantId);

        $this->assertEquals(
            $expectedPaymentsCount,
            count($paymentService->filterNoSpecialMerchantId($paymentModels))
        );
    }

    public function getTestData(): array
    {
        return [
            [
                Module::PAYMENT_PAYPAL_ID,
                'payPalMerchantId',
                1
            ],
            [
                Module::PAYMENT_PAYPAL_ID,
                '',
                0
            ],
            [
                Module::PAYMENT_KLARNA_LATER_ID,
                '',
                1
            ],
        ];
    }

    private function createPaymentMock(
        PaymentModel $payment,
        string $merchantId
    ): Payment {
        $moduleSettingsMock = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPayPalMerchantId'])
            ->getMock();
        $moduleSettingsMock->expects($payment->getId() === Module::PAYMENT_PAYPAL_ID ? $this->once() : $this->never())
            ->method('getPayPalMerchantId')
            ->willReturn($merchantId);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->setConstructorArgs(
                [
                    $this->createMock(Context::class),
                    $moduleSettingsMock,
                    $this->createMock(AdyenAPIResponsePayments::class),
                    $this->createMock(CountryRepository::class),
                    $this->createMock(AdyenAPILineItemsService::class),
                    $this->createMock(SessionSettings::class),
                    $this->createMock(OxNewService::class),
                    $this->createMock(UserAddress::class)
                ]
            )
            ->onlyMethods(['setPaymentExecutionError'])
            ->getMock();

        return $paymentMock;
    }

    private function createPaymentModelMock(
        string $paymentId
    ): PaymentModel {

        $paymentModelMock = $this->getMockBuilder(PaymentModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
        $paymentModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($paymentId);

        return $paymentModelMock;
    }
}
