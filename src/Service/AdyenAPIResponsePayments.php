<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;

/**
 * @extendable-class
 */
class AdyenAPIResponsePayments extends AdyenAPIResponse
{
    /**
     * @param AdyenAPIPayments $paymentMethodParams
     * @throws AdyenException
     * @return mixed
     */
    public function getPayments(AdyenAPIPayments $paymentMethodParams)
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $paymentMethodParams->getAdyenPaymentsParams();
            $result = $service->payments($params);
            if (!$result) {
                throw $this->getPaymentsNotFoundException();
            }
        } catch (AdyenException | Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
        return $result;
    }
}
