<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Service\Checkout;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPISession;

/**
 * @extendable-class
 */
class AdyenAPIResponseSession extends AdyenAPIResponse
{
    /**
     * @return string
     * @throws Exception
     */
    public function getAdyenSessionId(): string
    {
        $adyenSessionId = $this->session->getVariable(Module::ADYEN_SESSION_ID_NAME);
        if (!$adyenSessionId) {
            throw new Exception('Load the session before getting the session id');
        }
        return $adyenSessionId;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdyenSessionData(): string
    {
        $adyenSessionData = $this->session->getVariable(Module::ADYEN_SESSION_DATA_NAME);
        if (!$adyenSessionData) {
            throw new Exception('Load the session before getting the session data');
        }
        return $adyenSessionData;
    }

    /**
     * @param AdyenAPISession $sessionParams
     * @throws AdyenException
     */
    public function loadAdyenSession(AdyenAPISession $sessionParams): bool
    {
        $result = false;
        try {
            $service = $this->createCheckout();
            $params = $sessionParams->getAdyenSessionParams();
            $resultApi = $service->sessions($params);
            $result = $this->saveAdyenSession($resultApi);
            if (!$result) {
                throw new Exception('sessionData & id not found in Adyen-Response');
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
    public function saveAdyenSession(array $resultApi): bool
    {
        $adyenSessionData = $resultApi['sessionData'] ?? '';
        $adyenSessionId = $resultApi['id'] ?? '';
        $result = ($adyenSessionData && $adyenSessionId);
        $this->session->setVariable(Module::ADYEN_SESSION_DATA_NAME, $adyenSessionData);
        $this->session->setVariable(Module::ADYEN_SESSION_ID_NAME, $adyenSessionId);
        return $result;
    }

    public function deleteAdyenSession(): void
    {
        $this->session->deleteVariable(Module::ADYEN_SESSION_DATA_NAME);
        $this->session->deleteVariable(Module::ADYEN_SESSION_ID_NAME);
    }
}
