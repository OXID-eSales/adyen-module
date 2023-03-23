<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\User;
use PHPUnit\Framework\TestCase;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;

class UserAddressShopperNameTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\UserAddress::getAdyenShopperName
     */
    public function testGetAdyenShopperName()
    {
        $firstName = 'firstName';
        $lastName = 'lastName';
        $paymentController = $this->createPaymentControllerMock($firstName, $lastName);

        $this->assertEquals(
            json_encode($this->getShopperNameArray($firstName, $lastName)),
            $paymentController->getAdyenShopperName()
        );
    }

    private function createPaymentControllerMock(string $firstName, string $lastName): PaymentController
    {
        $addressMock = $this->getMockBuilder(Address::class)
            ->onlyMethods(['getAdyenStringData'])
            ->getMock();
        $addressReturnValueMap = [
            'oxfname' => $firstName,
            'oxlname' => $lastName,
        ];
        $addressMock->expects($this->exactly(2))
            ->method('getAdyenStringData')
            ->willReturnCallback(fn ($argument) => $addressReturnValueMap[$argument]);

        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getSelectedAddress'])
            ->getMock();
        $userMock->expects($this->once())
            ->method('getSelectedAddress')
            ->willReturn($addressMock);

        $paymentControllerMock = $this->getMockBuilder(PaymentController::class)
            ->onlyMethods(['getUser', 'arrayToJson'])
            ->getMock();

        $paymentControllerMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userMock);

        $paymentControllerMock->expects($this->once())
            ->method('arrayToJson')
            ->with($this->getShopperNameArray($firstName, $lastName))
            ->willReturn(json_encode($this->getShopperNameArray($firstName, $lastName)));

        return $paymentControllerMock;
    }

    private function getShopperNameArray(string $firstName, string $lastName): array
    {
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
        ];
    }
}
