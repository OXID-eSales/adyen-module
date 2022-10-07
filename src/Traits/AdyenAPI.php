<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPISession;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;

/**
 * Convenience trait to fetch Adyen API Services.
 * Use for example in classes where it's not possible to inject services in
 * the constructor because constructor is inherited from a shop core class.
 * Example: see module controllers
 */
trait AdyenAPI
{
    use ServiceContainer;

    protected ?Payment $adyenResponse = null;

    /**
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenSessionId(): string
    {
        $adyenResponse = $this->getAdyenSessionResponse();
        return $adyenResponse->getAdyenSessionId();
    }

    /**
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenSessionData(): string
    {
        $adyenResponse = $this->getAdyenSessionResponse();
        return $adyenResponse->getAdyenSessionData();
    }

    /**
     * @throws \Adyen\AdyenException
     */
    protected function getAdyenSessionResponse(): Payment
    {
        if (is_null($this->adyenResponse)) {
            $adyenAPISession = oxNew(AdyenAPISession::class);

            $context = $this->getServiceFromContainer(Context::class);
            $userRepository = $this->getServiceFromContainer(UserRepository::class);
            $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
            $adyenPayment = $this->getServiceFromContainer(Payment::class);

            $adyenAPISession->setCurrencyName($context->getActiveCurrencyName());

            $currencyDecimals = $context->getActiveCurrencyDecimals();
            $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
            $adyenAPISession->setCurrencyFilterAmount($currencyFilterAmount);

            $adyenAPISession->setCountryCode($userRepository->getUserCountryIso());

            $adyenAPISession->setMerchantAccount($moduleSettings->getMerchantAccount());

            $adyenAPISession->setReference(Module::ADYEN_ORDER_REFERENCE_ID);

            $adyenAPISession->setReturnUrl($context->getCurrentShopUrl() . 'index.php?cl=order');

            $adyenPayment->loadAdyenSession($adyenAPISession);
            $this->adyenResponse = $adyenPayment;
        }
        return $this->adyenResponse;
    }
}
