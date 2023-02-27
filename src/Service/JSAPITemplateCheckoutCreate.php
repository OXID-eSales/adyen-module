<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;

class JSAPITemplateCheckoutCreate
{
    private const NO_MAPPING_FOUND = 'no_create_id_found';

    private array $createIdMapping = [
        Module::PAYMENT_PAYPAL_ID => 'paypal',
        Module::PAYMENT_GOOGLE_PAY_ID => 'googlepay',
        Module::PAYMENT_KLARNA_ID => 'klarna',
        Module::PAYMENT_TWINT_ID => 'twint',
    ];

    public function getCreateId(string $paymentId): string
    {
        if (!isset($this->createIdMapping[$paymentId])) {
            return self::NO_MAPPING_FOUND;
        }

        return $this->createIdMapping[$paymentId];
    }
}
