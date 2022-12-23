<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use OxidEsales\Eshop\Core\Registry;

/**
 * @extendable-class
 */
class PaymentDetails
{
    public const PAYMENT_ERROR_NONE = 'ADYEN_PAYMENT_ERROR_NONE';
    public const PAYMENT_ERROR_GENERIC = 'ADYEN_PAYMENT_ERROR_GENERIC';

    private string $executionError = self::PAYMENT_ERROR_NONE;

    private array $paymentDetailsResult = [];

    private AdyenAPIResponsePaymentDetails $APIPaymentDetails;

    public function __construct(
        AdyenAPIResponsePaymentDetails $APIPaymentDetails
    ) {
        $this->APIPaymentDetails = $APIPaymentDetails;
    }

    public function setPaymentExecutionError(string $text): void
    {
        $this->executionError = $text;
    }

    public function getPaymentExecutionError(): string
    {
        return $this->executionError;
    }

    public function setPaymentDetailsResult(array $paymentResult): void
    {
        $this->paymentDetailsResult = $paymentResult;
    }

    public function getPaymentDetailsResult(): array
    {
        return $this->paymentDetailsResult;
    }

    /**
     * @param array $payments
     */
    public function collectPaymentDetails(array $payments): bool
    {
        $result = false;

        try {
            $resultPaymentDetails = $this->APIPaymentDetails->getPaymentDetails($payments);
            if (is_array($resultPaymentDetails)) {
                $this->setPaymentDetailsResult($resultPaymentDetails);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getPaymentDetails call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }
}
