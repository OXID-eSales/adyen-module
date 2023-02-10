<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;

class Module
{
    public function isAdyenPayment(string $paymentId): bool
    {
        return (isset(ModuleCore::PAYMENT_DEFINTIONS[$paymentId]));
    }

    public function showInPaymentCtrl(string $paymentId): bool
    {
        return ($this->isAdyenPayment($paymentId) &&
            ModuleCore::PAYMENT_DEFINTIONS[$paymentId]['paymentCtrl']);
    }

    public function isCaptureDelay(string $paymentId): bool
    {
        return ($this->isAdyenPayment($paymentId) &&
            ModuleCore::PAYMENT_DEFINTIONS[$paymentId]['capturedelay']); // @phpstan-ignore-line current payment
        // definitions always have capturedelay=true, phpstan would raise error
    }
}
