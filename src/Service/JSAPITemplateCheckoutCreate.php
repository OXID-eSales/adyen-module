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
        Module::PAYMENT_CREDITCARD_ID => 'oscadyencreditcard',
    ];

    /**
     * Payment methods that hand the browser fully off to a third-party page
     * (full redirect) instead of finishing inline on the order page. After
     * return, the form with ord_agb is gone, so OXID's AGB validation
     * cannot rely on the post submit of the order form.
     */
    private array $redirectPaymentIds = [
        Module::PAYMENT_TWINT_ID,
        Module::PAYMENT_KLARNA_LATER_ID,
        Module::PAYMENT_KLARNA_IMMEDIATE_ID,
        Module::PAYMENT_KLARNA_OVER_TIME_ID,
    ];

    public function getCreateId(string $paymentId): string
    {
        if (!isset($this->createIdMapping[$paymentId])) {
            return self::NO_MAPPING_FOUND;
        }

        return $this->createIdMapping[$paymentId];
    }

    public function isRedirectPayment(string $paymentId): bool
    {
        return in_array($paymentId, $this->redirectPaymentIds, true);
    }
}
