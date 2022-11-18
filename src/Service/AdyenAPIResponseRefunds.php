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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIRefunds;

/**
 * @extendable-class
 */
class AdyenAPIResponseRefunds extends AdyenAPIResponse
{
    /**
     * @param AdyenAPIRefunds $refundParams
     * @return mixed
     * @throws AdyenException
     */
    public function setRefund(AdyenAPIRefunds $refundParams): bool
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $refundParams->getAdyenRefundsParams();
            $result = $service->captures($params);
            if (!$result) {
                throw new Exception('payments not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
        return $result;
    }
}
