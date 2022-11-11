<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;

/**
 * @extendable-class
 */
class AdyenAPIResponsePayments extends AdyenAPIResponse
{
    /**
     * @param AdyenAPIPaymentMethods $paymentMethodParams
     * @throws AdyenException
     */
    public function getPayments(AdyenAPIPaymentMethods $paymentMethodParams): bool
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $paymentMethodParams->getAdyenPaymentMethodsParams();
            $result = $service->payments($params);
            if (!$result) {
                throw new Exception('payments not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
        return $result;
    }
}
