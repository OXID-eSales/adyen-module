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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;

/**
 * @extendable-class
 */
class AdyenAPIResponseCaptures extends AdyenAPIResponse
{
    /**
     * @param AdyenAPICaptures $captureParams
     * @return mixed
     * @throws AdyenException
     */
    public function setCapture(AdyenAPICaptures $captureParams)
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $captureParams->getAdyenCapturesParams();
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
