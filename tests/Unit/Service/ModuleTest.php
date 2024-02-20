<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;

class ModuleTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::isAdyenPayment
     */
    public function testIsAdyenPaymentTrue()
    {
        $paymentId = ModuleCore::PAYMENT_CREDITCARD_ID;
        $moduleService = oxNew(ModuleService::class);

        $this->assertTrue($moduleService->isAdyenPayment($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::isAdyenPayment
     */
    public function testIsAdyenPaymentFalse()
    {
        $paymentId = 'invalid';
        $moduleService = oxNew(ModuleService::class);

        $this->assertFalse($moduleService->isAdyenPayment($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::showInPaymentCtrl
     */
    public function testShowInPaymentCtrlTrue()
    {
        $paymentId = ModuleCore::PAYMENT_CREDITCARD_ID;
        $moduleService = oxNew(ModuleService::class);

        $this->assertTrue($moduleService->showInPaymentCtrl($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::showInPaymentCtrl
     */
    public function testShowInPaymentCtrlFalseInvalidPaymentId()
    {
        $paymentId = 'invalid';
        $moduleService = oxNew(ModuleService::class);

        $this->assertFalse($moduleService->showInPaymentCtrl($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::showInPaymentCtrl
     */
    public function testShowInPaymentCtrlFalseNoPaymentCtrl()
    {
        $paymentId = ModuleCore::PAYMENT_PAYPAL_ID;
        $moduleService = oxNew(ModuleService::class);

        $this->assertFalse($moduleService->showInPaymentCtrl($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::handleAssets
     */
    public function testHandleAssetsTrue()
    {
        $moduleService = oxNew(ModuleService::class);

        $paymentId = ModuleCore::PAYMENT_CREDITCARD_ID;
        $this->assertTrue($moduleService->handleAssets($paymentId));

        $paymentId = ModuleCore::PAYMENT_APPLE_PAY_ID;
        $this->assertTrue($moduleService->handleAssets($paymentId));

    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::handleAssets
     */
    public function testHandleAssetsFalseInvalidPaymentId()
    {
        $paymentId = 'invalid';
        $moduleService = oxNew(ModuleService::class);

        $this->assertFalse($moduleService->handleAssets($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::handleAssets
     */
    public function testHandleAssetsFalseNoPaymentCtrl()
    {
        $paymentId = ModuleCore::PAYMENT_PAYPAL_ID;
        $moduleService = oxNew(ModuleService::class);

        $this->assertFalse($moduleService->handleAssets($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::isCaptureDelay
     */
    public function testShowIsCaptureDelayTrue()
    {
        $paymentId = ModuleCore::PAYMENT_CREDITCARD_ID;
        $moduleService = oxNew(ModuleService::class);

        $this->assertTrue($moduleService->isCaptureDelay($paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Module::isCaptureDelay
     */
    public function testIsCaptureDelayFalseInvalidPaymentId()
    {
        $paymentId = 'invalid';
        $moduleService = oxNew(ModuleService::class);

        $this->assertFalse($moduleService->isCaptureDelay($paymentId));
    }

    /**
     * the last test case for isCaptureDelay that paymentId is valid but
     * capturedelay is false can't be tested because there is no payment
     * definition in ModuleCore::PAYMENT_DEFINTIONS which has capturedelay
     * false
     */
}
