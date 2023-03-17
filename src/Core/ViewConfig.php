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
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateConfiguration;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\PaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
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

    protected ModuleSettings $moduleSettings;
    protected Context $context;
    protected PaymentMethods $adyenPaymentMethods;
    protected CountryRepository $countryRepository;
    protected SessionSettings $sessionSettings;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $this->context = $this->getServiceFromContainer(Context::class);
        $this->adyenPaymentMethods = $this->getServiceFromContainer(PaymentMethods::class);
        $this->countryRepository = $this->getServiceFromContainer(CountryRepository::class);
        $this->sessionSettings = $this->getServiceFromContainer(SessionSettings::class);
    }

    /**
     * @throws AdyenException
     */
    public function checkAdyenHealth(): bool
    {
        $isHealthy = false;

        try {
            $isHealthy = $this->moduleSettings->checkConfigHealth() && $this->existsAdyenPaymentMethods();
        } catch (AdyenException $exception) {
            $this->getServiceFromContainer(LoggerInterface::class)->error(
                'ViewConfig::checkAdyenHealth could not prove existsAdyenPaymentMethods because of exception',
                ['exception' => $exception]
            );
        }

        return $isHealthy;
    }

    public function checkAdyenConfigHealth(): bool
    {
        return $this->moduleSettings->checkConfigHealth();
    }

    public function getAdyenOperationMode(): string
    {
        return $this->moduleSettings->getOperationMode();
    }

    public function getGooglePayOperationMode(): string
    {
        return $this->moduleSettings->getGooglePayOperationMode();
    }

    public function isAdyenLoggingActive(): bool
    {
        return $this->moduleSettings->isLoggingActive();
    }

    public function isAdyenAnalyticsActive(): bool
    {
        return $this->moduleSettings->isAnalyticsActive();
    }

    public function isAdyenSandboxMode(): bool
    {
        return $this->moduleSettings->isSandBoxMode();
    }

    public function getAdyenClientKey(): string
    {
        return $this->moduleSettings->getClientKey();
    }

    public function getAdyenPayPalMerchantId(): string
    {
        return $this->moduleSettings->getPayPalMerchantId();
    }

    public function getAdyenMerchantAccount(): string
    {
        return $this->moduleSettings->getMerchantAccount();
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

    public function getWebhookControllerUrl(): string
    {
        return $this->context->getWebhookControllerUrl();
    }

    /**
     * @throws AdyenException
     * @throws Exception
     */
    public function getAdyenPaymentMethods(): array
    {
        return $this->adyenPaymentMethods->getAdyenPaymentMethods();
    }

    /**
     * @throws AdyenException
     */
    public function existsAdyenPaymentMethods(): bool
    {
        return (bool)count($this->adyenPaymentMethods->getAdyenPaymentMethods());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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

    public function getTemplateConfiguration(
        FrontendController $oView,
        ?Payment $payment
    ): string {
        return $this->getServiceFromContainer(JSAPITemplateConfiguration::class)
            ->getConfiguration($this, $oView, $payment);
    }

    public function getTemplateCheckoutCreateId(string $paymentId): string
    {
        return $this->getServiceFromContainer(JSAPITemplateCheckoutCreate::class)
            ->getCreateId($paymentId);
    }

    public function getGooglePayTransactionInfo(): string
    {
        return $this->getServiceFromContainer(AdyenAPITransactionInfoService::class)
            ->getTransactionJson();
    }

    public function getTemplatePayButtonContainerId(string $paymentId): string
    {
        return $paymentId . '-container';
    }
}
