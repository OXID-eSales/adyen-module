<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidEsales\Eshop\Application\Model\Country as EshopModelCountry;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Country;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\UserAddress;
use PHPUnit\Framework\TestCase;

class UserAddressDeliveryAddressTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\UserAddress::getAdyenDeliveryAddress
     */
    public function testGetAdyenDeliveryAddress()
    {
        $countryId = '12345';
        $city = 'city';
        $country = 'DE';
        $postalCode = '12034';
        $houseNumberOrName = '12';
        $stateOrProvince = 'Berlin';
        $street = 'Unter den Linden';

        $user = $this->createUserMock(
            $countryId,
            $city,
            $postalCode,
            $houseNumberOrName,
            $stateOrProvince,
            $street
        );
        $oxNewService = $this->createOxNewServiceMock($countryId, $country);
        $userAddressService = new UserAddress($oxNewService);

        $this->assertEquals(
            $this->getDeliveryAddressArray(
                $city,
                $country,
                $postalCode,
                $houseNumberOrName,
                $stateOrProvince,
                $street
            ),
            $userAddressService->getAdyenDeliveryAddress($user)
        );
    }

    private function getDeliveryAddressArray(
        string $city,
        string $country,
        string $postalCode,
        string $houseNumberOrName,
        string $stateOrProvince,
        string $street
    ): array {
        return [
            'city' => $city,
            'country' => $country,
            'houseNumberOrName' => $houseNumberOrName,
            'postalCode' => $postalCode,
            'stateOrProvince' => $stateOrProvince,
            'street' => $street,
        ];
    }

    private function createAddressMock(
        string $countryId,
        string $city,
        string $postalCode,
        string $houseNumberOrName,
        string $stateOrProvince,
        string $street
    ): Address {
        $addressMock = $this->getMockBuilder(Address::class)
            ->onlyMethods(['getAdyenStringData'])
            ->getMock();
        $addressReturnValueMap = [
            'oxcountryid' => $countryId,
            'oxcity' => $city,
            'oxstreetnr' => $houseNumberOrName,
            'oxzip' => $postalCode,
            'oxstateid' => $stateOrProvince,
            'oxstreet' => $street,
        ];
        $addressMock->expects($this->exactly(6))
            ->method('getAdyenStringData')
            ->willReturnCallback(fn($argument) => $addressReturnValueMap[$argument]);

        return $addressMock;
    }

    private function createUserMock(
        string $countryId,
        string $city,
        string $postalCode,
        string $houseNumberOrName,
        string $stateOrProvince,
        string $street
    ): User {
        $userMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getSelectedAddress'])
            ->getMock();
        $userMock->expects($this->once())
            ->method('getSelectedAddress')
            ->willReturn(
                $this->createAddressMock(
                    $countryId,
                    $city,
                    $postalCode,
                    $houseNumberOrName,
                    $stateOrProvince,
                    $street
                )
            );

        return $userMock;
    }

    private function createCountryMock(string $countryId, string $country): Country
    {
        $countryMock = $this->getMockBuilder(Country::class)
            ->onlyMethods(['load', 'getAdyenStringData'])
            ->getMock();
        $countryMock->expects($this->once())
            ->method('load')
            ->with($countryId);
        $countryMock->expects($this->once())
            ->method('getAdyenStringData')
            ->with('oxisoalpha2')
            ->willReturn($country);

        return $countryMock;
    }

    private function createOxNewServiceMock(string $countryId, string $country): OxNewService
    {
        $oxNewServiceMock = $this->getMockBuilder(OxNewService::class)
            ->onlyMethods(['oxNew'])
            ->getMock();
        $oxNewServiceMock->expects($this->once())
            ->method('oxNew')
            ->with(EshopModelCountry::class)
            ->willReturn($this->createCountryMock($countryId, $country));

        return $oxNewServiceMock;
    }
}
