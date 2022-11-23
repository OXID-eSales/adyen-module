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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;

/**
 * @extendable-class
 */
class AdyenAPIResponseCancels extends AdyenAPIResponse
{
    /**
     * @param AdyenAPICancels $cancelParams
     * @throws AdyenException
     * @return mixed
     */
    public function setCancel(AdyenAPICancels $cancelParams)
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $cancelParams->getAdyenCancelParams();
            $result = $service->cancels($params);
            if (!$result) {
                throw new Exception('payments not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
        return $result;
    }
}
