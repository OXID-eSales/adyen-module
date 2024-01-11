<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Core;

use Adyen\AdyenException;
use Exception;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPITransactionInfoService;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateConfiguration;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\PaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidSolutionCatalysts\Adyen\Traits\Json;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) is too high, hard to refactor
 */
class ViewConfig extends ViewConfig_parent
{
    use Json;
    use ServiceContainer;
    use AdyenPayment;

    protected ?ModuleSettings $adyenModuleSettings = null;
    protected ?Context $adyenContext = null;
    protected ?PaymentMethods $adyenPaymentMethods = null;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyen(): bool
    {
        return $this->getModuleSettingsSrvc()->checkConfigHealth();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function checkAdyenHealth(): bool
    {
        $isHealthy = false;

        try {
            $isHealthy = $this->getModuleSettingsSrvc()->checkConfigHealth() && $this->existsAdyenPaymentMethods();
        } catch (AdyenException $exception) {
            $this->getServiceFromContainer(LoggerInterface::class)->error(
                'ViewConfig::checkAdyenHealth could not prove existsAdyenPaymentMethods because of exception',
                ['exception' => $exception]
            );
        }

        return $isHealthy;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function checkAdyenConfigHealth(): bool
    {
        return $this->getModuleSettingsSrvc()->checkConfigHealth();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenOperationMode(): string
    {
        return $this->getModuleSettingsSrvc()->getOperationMode();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getGooglePayOperationMode(): string
    {
        return $this->getModuleSettingsSrvc()->getGooglePayOperationMode();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isAdyenLoggingActive(): bool
    {
        return $this->getModuleSettingsSrvc()->isLoggingActive();
    }

    public function isAdyenPaymentOxChecked(): bool
    {
        /** @var Payment $oPayment */
        $oPayment = oxNew(Payment::class);
        $oPayment->load('oscadyencreditcard');
        return $oPayment->oxpayments__oxchecked->value;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isAdyenAnalyticsActive(): bool
    {
        return $this->getModuleSettingsSrvc()->isAnalyticsActive();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isAdyenSandboxMode(): bool
    {
        return $this->getModuleSettingsSrvc()->isSandBoxMode();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenClientKey(): string
    {
        return $this->getModuleSettingsSrvc()->getClientKey();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenPayPalMerchantId(): string
    {
        return $this->getModuleSettingsSrvc()->getPayPalMerchantId();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenMerchantAccount(): string
    {
        return $this->getModuleSettingsSrvc()->getMerchantAccount();
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

    public function getAdyenHtmlParamPspReferenceName(): string
    {
        return Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME;
    }

    public function getAdyenHtmlParamAmountValueName(): string
    {
        return Module::ADYEN_HTMLPARAM_AMOUNTVALUE_NAME;
    }

    public function getAdyenHtmlParamResultCodeName(): string
    {
        return Module::ADYEN_HTMLPARAM_RESULTCODE_NAME;
    }

    public function getAdyenHtmlParamAmountCurrencyName(): string
    {
        return Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME;
    }

    public function getAdyenPaymentCreditCardId(): string
    {
        return Module::PAYMENT_CREDITCARD_ID;
    }

    public function getAdyenPaymentPayPalId(): string
    {
        return Module::PAYMENT_PAYPAL_ID;
    }

    public function getAdyenPaymentGooglePayId(): string
    {
        return Module::PAYMENT_GOOGLE_PAY_ID;
    }

    public function getAdyenErrorInvalidSession(): string
    {
        return Module::ADYEN_ERROR_INVALIDSESSION_NAME;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getWebhookControllerUrl(): string
    {
        return $this->getContextSrvc()->getWebhookControllerUrl();
    }

    /**
     * @throws AdyenException
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenPaymentMethods(): array
    {
        return $this->getPaymentMethodsSrvc()->getAdyenPaymentMethods();
    }

    /**
     * @throws AdyenException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function existsAdyenPaymentMethods(): bool
    {
        return (bool)count($this->getPaymentMethodsSrvc()->getAdyenPaymentMethods());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenShopperLocale(): string
    {
        return $this->getServiceFromContainer(UserRepository::class)->getUserLocale();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenCountryIso(): string
    {
        return $this->getServiceFromContainer(CountryRepository::class)
            ->getCountryIso();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenAmountValue(): string
    {
        /** @var Basket $basket */
        $basket = Registry::getSession()->getBasket();
        $amount = $basket->getPrice()->getBruttoPrice();
        return $this->getAdyenAmount(
            $amount,
            $this->getContextSrvc()->getActiveCurrencyDecimals()
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAdyenAmountCurrency(): string
    {
        return $this->getContextSrvc()->getActiveCurrencyName();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTemplateConfiguration(
        FrontendController $oView,
        ?Payment $payment
    ): string {
        return $this->getServiceFromContainer(JSAPITemplateConfiguration::class)
            ->getConfiguration($this, $oView, $payment);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTemplateCheckoutCreateId(?Payment $payment): string
    {
        return $payment ? $this->getServiceFromContainer(JSAPITemplateCheckoutCreate::class)
            ->getCreateId($payment->getId())
            : '';
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getGooglePayTransactionInfo(): string
    {
        return $this->getServiceFromContainer(AdyenAPITransactionInfoService::class)
            ->getTransactionJson();
    }

    public function getTemplatePayButtonContainerId(?Payment $payment): string
    {
        return $payment ? $payment->getId() . '-container' : '';
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getModuleSettingsSrvc(): ModuleSettings
    {
        if (is_null($this->adyenModuleSettings)) {
            $this->adyenModuleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        }
        return $this->adyenModuleSettings;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getContextSrvc(): Context
    {
        if (is_null($this->adyenContext)) {
            $this->adyenContext = $this->getServiceFromContainer(Context::class);
        }
        return $this->adyenContext;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getPaymentMethodsSrvc(): PaymentMethods
    {
        if (is_null($this->adyenPaymentMethods)) {
            $this->adyenPaymentMethods = $this->getServiceFromContainer(PaymentMethods::class);
        }
        return $this->adyenPaymentMethods;
    }
}
