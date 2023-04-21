<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Controller\PaymentController;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Service\JSAPIConfigurationService;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JSAPIConfigurationServiceTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\JSAPIConfigurationService::getConfigFieldsAsArray
     * @covers \OxidSolutionCatalysts\Adyen\Service\JSAPIConfigurationService::__construct
     * @covers \OxidSolutionCatalysts\Adyen\Service\JSAPIConfigurationService::getPaymentPageConfigFields
     * @covers \OxidSolutionCatalysts\Adyen\Service\JSAPIConfigurationService::getOrderPageConfigFields
     */
    public function testGetConfiguration(): void
    {
        $service = $this->getServiceFromContainer(JSAPIConfigurationService::class);
        /** @var ViewConfig $viewConfig */
        $viewConfig = $this->createViewConfigMock();
        $payment = new Payment();
        $payment->setId(Module::PAYMENT_PAYPAL_ID);
        /** @var User $user */
        $user = $this->createUserMock();

        $this->assertEquals(
            [
                'environment' => 'test',
                'clientKey' => 'key',
                'analytics' => [
                    'enabled' => false,
                ],
                'locale' => 'DE',
                'deliveryAddress' => [
                    'city' => 'city',
                    'country' => '',
                    'houseNumberOrName' => 'streetnr',
                    'postalCode' => 'zip',
                    'stateOrProvince' => 'state',
                    'street' => 'street',
                ],
                'shopperName' => [
                    'firstName' => 'fname',
                    'lastName' => 'lname',
                ],
                'shopperEmail' => 'email',
                'shopperReference' => '123456789',
                'shopperIP' => '127.0.0.1',
                'showPayButton' => true,
            ],
            $service->getConfigFieldsAsArray($viewConfig, $user, $payment)
        );
    }

    private function createViewConfigMock(): MockObject
    {
        $mock = $this->createMock(ViewConfig::class);

        $mock->expects($this->any())
            ->method('getAdyenOperationMode')
            ->willReturn(ModuleSettings::OPERATION_MODE_SANDBOX);

        $mock->expects($this->any())
            ->method('getAdyenClientKey')
            ->willReturn('key');

        $mock->expects($this->any())
            ->method('isAdyenLoggingActive')
            ->willReturn(false);

        $mock->expects($this->any())
            ->method('getAdyenShopperLocale')
            ->willReturn('DE');

        $mock->expects($this->any())
            ->method('getRemoteAddress')
            ->willReturn('127.0.0.1');

        $mock->expects($this->any())
            ->method('getAdyenPaymentMethods')
            ->willReturn(['pay' => true]);

        return $mock;
    }

    private function createUserMock(): MockObject
    {
        $addressMock = $this->createMock(Address::class);

        $addressMock->expects($this->any())
            ->method('getAdyenStringData')
            ->withConsecutive(
                ['oxcountryid'],
                ['oxcity'],
                ['oxstreetnr'],
                ['oxzip'],
                ['oxstateid'],
                ['oxstreet'],
                ['oxfname'],
                ['oxlname']
            )
            ->willReturnOnConsecutiveCalls(
                '123',
                'city',
                'streetnr',
                'zip',
                'state',
                'street',
                'fname',
                'lname'
            );

        $userMock = $this->createMock(User::class);

        $userMock->expects($this->any())
            ->method('getId')
            ->willReturn('123456789');

        $userMock->expects($this->any())
            ->method('getSelectedAddress')
            ->willReturn($addressMock);

        $userMock->expects($this->any())
            ->method('getAdyenStringData')
            ->with('oxusername')
            ->willReturn('email');

        return $userMock;
    }
}
