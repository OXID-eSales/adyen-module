<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Core;

use Adyen\AdyenException;
use Exception;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidSolutionCatalysts\Adyen\Traits\Json;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class ViewConfig extends ViewConfig_parent
{
    use Json;
    use ServiceContainer;
    use AdyenPayment;

    protected ModuleSettings $moduleSettings;
    protected Context $context;
    protected Payment $adyenPayment;
    protected CountryRepository $countryRepository;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $this->context = $this->getServiceFromContainer(Context::class);
        $this->adyenPayment = $this->getServiceFromContainer(Payment::class);
        $this->countryRepository = $this->getServiceFromContainer(CountryRepository::class);
    }

    public function checkAdyenHealth(): bool
    {
        return (
            $this->moduleSettings->checkConfigHealth() &&
            $this->existsAdyenPaymentMethods()
        );
    }

    public function checkAdyenConfigHealth(): bool
    {
        return $this->moduleSettings->checkConfigHealth();
    }

    public function getAdyenOperationMode(): string
    {
        return $this->moduleSettings->getOperationMode();
    }

    public function isAdyenLoggingActive(): bool
    {
        return $this->moduleSettings->isLoggingActive();
    }

    public function getAdyenClientKey(): string
    {
        return $this->moduleSettings->getClientKey();
    }

    public function getAdyenPayPalMerchantId(): string
    {
        return $this->moduleSettings->getPayPalMerchantId();
    }

    public function getAdyenSDKVersion(): string
    {
        return Module::ADYEN_SDK_VERSION;
    }

    public function getAdyenIntegrityJS(): string
    {
        return Module::ADYEN_INTEGRITY_JS;
    }

    public function getAdyenIntegrityCSS(): string
    {
        return Module::ADYEN_INTEGRITY_CSS;
    }

    public function getAdyenHtmlParamStateName(): string
    {
        return Module::ADYEN_HTMLPARAM_PAYMENTSTATEDATA_NAME;
    }

    public function getWebhookControllerUrl(): string
    {
        return $this->context->getWebhookControllerUrl();
    }

    /**
     * return a JSON-String with PaymentMethods (array)
     * @throws AdyenException
     * @throws Exception
     */
    public function getAdyenPaymentMethods(): string
    {
        return $this->arrayToJson($this->adyenPayment->getAdyenPaymentMethods());
    }

    /**
     * @throws AdyenException
     */
    public function existsAdyenPaymentMethods(): bool
    {
        return (bool)count($this->adyenPayment->getAdyenPaymentMethods());
    }

    public function getAdyenShopperLocale(): string
    {
        return $this->getServiceFromContainer(UserRepository::class)->getUserLocale();
    }

    public function getAdyenCountryIso(): string
    {
        return $this->countryRepository->getCountryIso();
    }

    public function getAdyenAmountValue(): string
    {
        /** @var Basket $basket */
        $basket = Registry::getSession()->getBasket();
        $amount = $basket->getPrice()->getBruttoPrice();
        return $this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        );
    }

    public function getAdyenAmountCurrency(): string
    {
        return $this->context->getActiveCurrencyName();
    }

}
