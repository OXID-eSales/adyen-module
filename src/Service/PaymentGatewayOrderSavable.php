<?php

namespace OxidSolutionCatalysts\Adyen\Service;

class PaymentGatewayOrderSavable
{
    public function prove(string $pspReference, string $resultCode, string $orderReference): bool
    {
        return $pspReference && $resultCode && $orderReference;
    }
}
