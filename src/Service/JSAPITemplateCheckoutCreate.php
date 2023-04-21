<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;

class JSAPITemplateCheckoutCreate
{
    private const NO_MAPPING_FOUND = 'no_create_id_found';

    private array $createIdMapping = [
        Module::PAYMENT_PAYPAL_ID => 'paypal',
        Module::PAYMENT_GOOGLE_PAY_ID => 'googlepay',
        Module::PAYMENT_TWINT_ID => 'twint',
        Module::PAYMENT_APPLE_PAY_ID => 'applepay',
        Module::PAYMENT_KLARNA_LATER_ID => 'klarna',
        Module::PAYMENT_KLARNA_IMMEDIATE_ID => 'klarna_paynow',
        Module::PAYMENT_KLARNA_OVER_TIME_ID => 'klarna_account',
    ];

    public function getCreateId(string $paymentId): string
    {
        if (!isset($this->createIdMapping[$paymentId])) {
            return self::NO_MAPPING_FOUND;
        }

        return $this->createIdMapping[$paymentId];
    }
}
