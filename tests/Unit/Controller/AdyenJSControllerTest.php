<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Controller;

use OxidEsales\Eshop\Core\Header;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Controller\AdyenJSController;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPISession;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;

class AdyenJSControllerTest extends UnitTestCase
{
    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testGetAdyenJsonSession(): void
    {
        $utilsStub = $this->createPartialMock(Utils::class, ['showMessageAndExit']);
        $utilsStub->expects($this->once())
            ->method('showMessageAndExit')
            ->with($this->equalTo("{\n    \"id\": \"test\",\n    \"data\": \"test\"\n}"));

        Registry::set(Utils::class, $utilsStub);

        // set DummyData as Response ...
        $controller = $this->createPartialMock(AdyenJSController::class, ['getAdyenSessionId', 'getAdyenSessionData']);
        $controller->expects($this->any())->method('getAdyenSessionId')->willReturn('test');
        $controller->expects($this->any())->method('getAdyenSessionData')->willReturn('test');

        $controller->getAdyenJsonSession();

        $header = Registry::get(Header::class)->getHeader();
        $this->assertContains("Cache-Control: no-cache\r\n", $header);
        $this->assertContains("Content-Type: application/json\r\n", $header);
        $this->assertContains("Status: 200 OK\r\n", $header);
    }

    public function testGetAdyenSessionIdAndData(): void
    {
        $paymentStub = $this->createPartialMock(Payment::class, ['getAdyenSessionId', 'getAdyenSessionData']);
        $paymentStub->method('getAdyenSessionId')->willReturn('test1');
        $paymentStub->method('getAdyenSessionData')->willReturn('test2');

        $controller = $this->createPartialMock(AdyenJSController::class, ['getAdyenSessionResponse']);
        $controller->method('getAdyenSessionResponse')->willReturn($paymentStub);

        $this->assertSame('test1', $controller->getAdyenSessionId());
        $this->assertSame('test2', $controller->getAdyenSessionData());
    }

    public function testGetAdyenSessionResponse(): void
    {
        $contextStub = $this->createPartialMock(
            Context::class,
            [
                'getActiveCurrencyName',
                'getActiveCurrencyDecimals',
                'getCurrentShopUrl'
            ]);
        $contextStub->method('getActiveCurrencyName')->willReturn('testCurrencyName');
        $contextStub->method('getActiveCurrencyDecimals')->willReturn(2);
        $contextStub->method('getCurrentShopUrl')->willReturn('https://www.dummy.dev');

        $userRepositoryStub = $this->createPartialMock(UserRepository::class, ['getUserCountryIso']);
        $userRepositoryStub->method('getUserCountryIso')->willReturn('DE');

        $moduleSettingsStub = $this->createPartialMock(ModuleSettings::class, ['getMerchantAccount', 'getAdyenSessionData']);
        $moduleSettingsStub->method('getMerchantAccount')->willReturn('TestMerchant');

        $paymentStub = $this->createPartialMock(Payment::class, ['loadAdyenSession']);
        $paymentStub->method('loadAdyenSession')->willReturn(true);

        $adyenAPISessionStub = $this->createPartialMock(
            AdyenAPISession::class, [
            'setCurrencyName',
            'setCurrencyFilterAmount',
            'setCountryCode',
            'setReference',
            'setReturnUrl'
        ]);

        $adyenAPISessionStub->expects($this->once())->method('setCurrencyName')->with(
            $contextStub->getActiveCurrencyName()
        );
        $adyenAPISessionStub->expects($this->once())->method('setCurrencyFilterAmount')->with(
            '10' . str_repeat('0', (int)$contextStub->getActiveCurrencyName())
        );
        $adyenAPISessionStub->expects($this->once())->method('setCountryCode')->with(
            $userRepositoryStub->getUserCountryIso()
        );
        $adyenAPISessionStub->expects($this->once())->method('setReference')->with(
            Module::ADYEN_ORDER_REFERENCE_ID
        );
        $adyenAPISessionStub->expects($this->once())->method('setReturnUrl')->with(
            $contextStub->getCurrentShopUrl() . 'index.php?cl=order'
        );
        $paymentStub->expects($this->once())->method('loadAdyenSession')->with(
            $adyenAPISessionStub
        );

        $controller = $this->createPartialMock(AdyenJSController::class, ['getAdyenSessionResponse']);
        $controller->method('getAdyenSessionResponse')->willReturn($paymentStub);

        //$this->assertInstanceOf(Payment::class, $controller->getAdyenSessionResponse());
    }
}