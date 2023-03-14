<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;

/**
 * @extendable-class
 */
class AdyenAPIResponseCancels extends AdyenAPIResponse
{
    /**
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
                throw $this->getPaymentsNotFoundException();
            }
        } catch (AdyenException | Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
        return $result;
    }
}
