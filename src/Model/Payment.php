<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Service\PaymentConfigService;
use OxidSolutionCatalysts\Adyen\Traits\DataGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Core\Module as CoreModule;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Payment
 */
class Payment extends Payment_parent
{
    use ServiceContainer;
    use DataGetter;

    private PaymentConfigService $paymentConfigService;

    public function __construct()
    {
        parent::__construct();

        $this->paymentConfigService = $this->getServiceFromContainer(PaymentConfigService::class);
    }

    /**
     * Checks if the payment method is an Adyen payment method
     */
    public function isAdyenPayment(): bool
    {
        return $this->paymentConfigService->isAdyenPayment($this->getId());
    }

    /**
     * Checks if the payment method is show on Payment Controller
     */
    public function showInPaymentCtrl(): bool
    {
        return ($this->isAdyenPayment() &&
            $this->getServiceFromContainer(ModuleService::class)->showInPaymentCtrl($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment method is show on Order Controller
     */
    public function showInOrderCtrl(): bool
    {
        return ($this->isAdyenPayment() &&
            !$this->getServiceFromContainer(ModuleService::class)->showInPaymentCtrl($this->getId()) &&
            $this->getAdyenBoolData('oxactive') === true
        );
    }

    /**
     * Checks if the payment allow manual Capture
     */
    public function isAdyenManualCapture(): bool
    {
        return $this->paymentConfigService->isAdyenManualCapture($this->getId());
    }

    /**
     * Checks if the payment allow immediate Capture
     */
    public function isAdyenImmediateCapture(): bool
    {
        return $this->paymentConfigService->isAdyenImmediateCapture($this->getId());
    }

    /**
     * for some payments the id and the template id used in frontend/tpl/payment/adyen_order_submit.tpl:2 differs
     */
    public function getTemplateId(): string
    {
        if ($this->getId() === CoreModule::PAYMENT_GOOGLE_PAY_ID) {
            return 'googlepay';
        }

        return $this->getId();
    }
}
