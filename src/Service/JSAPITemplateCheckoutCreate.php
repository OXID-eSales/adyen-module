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
    ];

    private ModuleSettings $moduleSettings;

    public function __construct(ModuleSettings $moduleSettings)
    {
        $this->moduleSettings = $moduleSettings;
    }

    public function getCreateId(string $paymentId): string
    {
        if (Module::PAYMENT_KLARNA_ID === $paymentId) {
            return $this->getKlarnaCreateId();
        }

        if (!isset($this->createIdMapping[$paymentId])) {
            return self::NO_MAPPING_FOUND;
        }

        return $this->createIdMapping[$paymentId];
    }

    private function getKlarnaCreateId(): string
    {
        return $this->moduleSettings->getKlarnaPaymentType();
    }
}
