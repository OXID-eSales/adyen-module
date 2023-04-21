<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;

/**
 * @extendable-class
 */
class AdyenAPIResponsePaymentDetails extends AdyenAPIResponse
{
    /**
     * @param array $paymentParams
     * @throws AdyenException
     * @return mixed
     */
    public function getPaymentDetails(array $paymentParams)
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $result = $service->paymentsDetails($paymentParams);
            if (!$result) {
                throw new Exception('paymentdetails not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
        return $result;
    }
}
