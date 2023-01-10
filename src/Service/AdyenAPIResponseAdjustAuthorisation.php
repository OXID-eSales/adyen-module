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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIAdjustAuthorisation;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentsUpdate;

/**
 * @extendable-class
 */
class AdyenAPIResponseAdjustAuthorisation extends AdyenAPIResponse
{
    /**
     * @param AdyenAPIAdjustAuthorisation $adjustAuthorisationParams
     * @throws AdyenException
     * @return mixed
     */
    public function getAdjustAuthorisation(AdyenAPIAdjustAuthorisation $adjustAuthorisationParams)
    {
        $result = false;
        try {
            $service = $this->createModification();
            $params = $adjustAuthorisationParams->getAdyenAdjustAuthorisationParams();
            $result = $service->adjustAuthorisation($params);
            if (!$result) {
                throw new Exception('adjustAuthorisation not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
        return $result;
    }
}
