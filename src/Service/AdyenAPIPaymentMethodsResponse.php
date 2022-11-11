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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;

/**
 * @extendable-class
 */
class AdyenAPIPaymentMethodsResponse
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var Session
     */
    private Session $session;

    public function __construct(
        AdyenSDKLoader $adyenSDK,
        Session $session
    ) {
        $this->client = $adyenSDK->getAdyenSDK();
        $this->session = $session;
    }

    /**
     * @throws Exception
     */
    public function getAdyenPaymentMethods(): string
    {
        $adyenPaymentMethods = $this->session->getVariable(Module::ADYEN_SESSION_PAYMENTMETHODS_NAME);
        if (!$adyenPaymentMethods) {
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
        $this->session->setVariable(Module::ADYEN_SESSION_PAYMENTMETHODS_NAME, $paymentMethods);
        return $result;
    }

    public function deleteAdyenPaymentMethods(): void
    {
        $this->session->deleteVariable(Module::ADYEN_SESSION_PAYMENTMETHODS_NAME);
    }

    /**
     * @return Checkout
     * @throws AdyenException
     */
    protected function createCheckout(): Checkout
    {
        return new Checkout($this->client);
    }
}
