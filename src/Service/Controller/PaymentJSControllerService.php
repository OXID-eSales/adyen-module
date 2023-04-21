<?php

namespace OxidSolutionCatalysts\Adyen\Service\Controller;

use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class PaymentJSControllerService
{
    private PaymentCancel $paymentCancel;
    private SessionSettings $sessionSettings;

    public function __construct(
        PaymentCancel $paymentCancel,
        SessionSettings $sessionSettings
    ) {
        $this->paymentCancel = $paymentCancel;
        $this->sessionSettings = $sessionSettings;
    }

    /**
     * check if a AdyenAuthorisation exists and a cancel is necessary
     */
    public function cancelPaymentIfNecessary(
        string $pspReference,
        float $basketAmountValue,
        string $orderReference
    ): void {
        if ($pspReference && $this->sessionSettings->getAmountValue() < $basketAmountValue) {
            $this->paymentCancel->doAdyenCancel(
                $pspReference,
                $orderReference
            );
        }
    }

    /**
     * create orderReference if empty and save the amount to the session for AdyenAuthorisation-check
     */
    public function createOrderReference(string $orderReference, float $basketAmountValue): string
    {
        if (!$orderReference) {
            $this->sessionSettings->setAmountValue($basketAmountValue);
            return $this->sessionSettings->createOrderReference();
        }

        return $orderReference;
    }
}
