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
class AdyenAPIResponsePaymentMethods extends AdyenAPIResponse
{
    /**
     * @throws Exception
     */
    public function getAdyenPaymentMethods(): array
    {
        $adyenPaymentMethods = $this->session->getPaymentMethods();
        if (!count($adyenPaymentMethods)) {
            throw new Exception('Load the paymentMethods before getting the paymentMethods');
        }
        return $adyenPaymentMethods;
    }

    /**
     * @param AdyenAPIPaymentMethods $paymentMethodParams
     * @throws AdyenException
     */
    public function loadAdyenPaymentMethods(AdyenAPIPaymentMethods $paymentMethodParams): bool
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $paymentMethodParams->getAdyenPaymentMethodsParams();
            $resultApi = $service->paymentMethods($params);
            $result = $this->saveAdyenPaymentMethods($resultApi);
            if (!$result) {
                throw new Exception('paymentMethodsData not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
        return $result;
    }

    /**
     * @param array $resultApi
     * @return bool
     * @throws AdyenException
     */
    public function saveAdyenPaymentMethods(array $resultApi): bool
    {
        $paymentMethods = $resultApi['paymentMethods'] ? $resultApi : '';
        $result = (bool)$paymentMethods;
        $this->session->setPaymentMethods($paymentMethods);
        return $result;
    }

    public function deleteAdyenPaymentMethods(): void
    {
        $this->session->deletePaymentMethods();
    }
}
