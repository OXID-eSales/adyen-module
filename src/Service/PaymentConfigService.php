<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings as ModuleSettingsService;

class PaymentConfigService
{
    private Module $moduleService;
    private ModuleSettings $moduleSettingsService;

    public function __construct(
        ModuleService $moduleService,
        ModuleSettingsService $moduleSettingsService
    ) {

        $this->moduleService = $moduleService;
        $this->moduleSettingsService = $moduleSettingsService;
    }

    /**
     * Checks if the payment method is an Adyen payment method
     */
    public function isAdyenPayment(string $paymentId): bool
    {
        return $this->moduleService->isAdyenPayment($paymentId);
    }

    /**
     * Checks if the payment allow manual Capture
     */
    public function isAdyenManualCapture(string $paymentId): bool
    {
        return ($this->moduleService->isCaptureDelay($paymentId) &&
            $this->moduleSettingsService->isManualCapture($paymentId)
        );
    }

    /**
     * Checks if the payment allow immediate Capture
     */
    public function isAdyenImmediateCapture(string|null $paymentId): bool
    {
        return (!is_null($paymentId) &&
            $this->moduleService->isCaptureDelay($paymentId) &&
            $this->moduleSettingsService->isImmediateCapture($paymentId)
        );
    }
}
