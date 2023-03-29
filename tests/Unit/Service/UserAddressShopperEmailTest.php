<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\UserAddress;
use PHPUnit\Framework\TestCase;

class UserAddressShopperEmailTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\UserAddress::getAdyenShopperEmail
     */
    public function testGetAdyenShopperEmail()
    {
        $shopperEmail = 'shopperEmail';
        $user = $this->createUserMock($shopperEmail);
        $userAddressService = new UserAddress(new OxNewService());

        $this->assertEquals(
            $shopperEmail,
            $userAddressService->getAdyenShopperEmail($user)
        );
    }

    private function createUserMock(string $shopperEmail): User
    {
        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAdyenStringData'])
            ->getMock();
        $userMock->expects($this->once())
            ->method('getAdyenStringData')
            ->with('oxusername')
            ->willReturn($shopperEmail);

        return $userMock;
    }
}
