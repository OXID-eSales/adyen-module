<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\UserAddress;
use PHPUnit\Framework\TestCase;

class UserAddressShopperNameTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\UserAddress::getAdyenShopperName
     */
    public function testGetAdyenShopperName()
    {
        $firstName = 'firstName';
        $lastName = 'lastName';
        $user = $this->createUserMock($firstName, $lastName);
        $userAddressService = new UserAddress(new OxNewService());


        $this->assertEquals(
            $this->getShopperNameArray($firstName, $lastName),
            $userAddressService->getAdyenShopperName($user)
        );
    }

    private function createUserMock(string $firstName, string $lastName): User
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

        return $userMock;
    }

    private function getShopperNameArray(string $firstName, string $lastName): array
    {
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
        ];
    }
}
