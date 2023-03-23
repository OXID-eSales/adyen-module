<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use OxidSolutionCatalysts\Adyen\Model\User;
use PHPUnit\Framework\TestCase;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;

class UserAddressShopperEmailTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\UserAddress::getAdyenShopperEmail
     */
    public function testGetAdyenShopperEmail()
    {
        $shopperEmail = 'shopperEmail';
        $paymentController = $this->createPaymentControllerMock($shopperEmail);

        $this->assertEquals(
            $shopperEmail,
            $paymentController->getAdyenShopperEmail()
        );
    }

    private function createPaymentControllerMock(string $shopperEmail): PaymentController
    {
        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAdyenStringData'])
            ->getMock();
        $userMock->expects($this->once())
            ->method('getAdyenStringData')
            ->with('oxusername')
            ->willReturn($shopperEmail);

        $paymentControllerMock = $this->getMockBuilder(PaymentController::class)
            ->onlyMethods(['getUser'])
            ->getMock();

        $paymentControllerMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userMock);

        return $paymentControllerMock;
    }
}
