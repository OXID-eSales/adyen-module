<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\EshopCommunity\Application\Controller\FrontendController;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidSolutionCatalysts\Adyen\Controller\OrderController;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig as AdyenViewConfig;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Model\Payment as AdyenPayment;
use Psr\Log\LoggerInterface;

class JSAPITemplateConfiguration
{
    private TemplateEngineInterface $templateEngine;
    private LoggerInterface $logger;
    private JSAPIConfigurationService $configurationService;
    private AdyenAPIResponsePaymentMethods $ApiResponsePaymentMethodsService;
    private ModuleSettings $moduleSettings;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        JSAPIConfigurationService $configurationService,
        AdyenAPIResponsePaymentMethods $ApiResponsePaymentMethodsService,
        LoggerInterface $logger,
        ModuleSettings $moduleSettings
    ) {
        $this->templateEngine = $templateEngine;
        $this->logger = $logger;
        $this->configurationService = $configurationService;
        $this->ApiResponsePaymentMethodsService = $ApiResponsePaymentMethodsService;
        $this->moduleSettings = $moduleSettings;
    }

    public function getConfiguration(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): string {
        return $this->templateEngine->render(
            '@'.Module::MODULE_ID.'/payment/adyen_assets_configuration.html.twig',
            $this->getViewData($viewConfig, $controller, $payment)
        );
    }

    public function isApplePay(
        FrontendController $controller,
        ?Payment $payment
    ): bool {
        $paymentId = $payment instanceof Payment ? $payment->getId() : '';
        return $paymentId === Module::PAYMENT_APPLE_PAY_ID;
    }

    private function getViewData(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): array {
        $paymentId = $payment instanceof Payment ? $payment->getId() : '';
        /** @var AdyenViewConfig $viewConfig */
        return [
            'configFields' => $this->getConfigFieldsJsonFormatted($viewConfig, $controller, $payment),
            'isLog' => $viewConfig->isAdyenLoggingActive(),
            'isPaymentPage' => $controller instanceof PaymentController,
            'isOrderPage' => $controller instanceof OrderController,
            'orderPaymentPayPal' => $controller instanceof OrderController
                && $paymentId === Module::PAYMENT_PAYPAL_ID,
            'orderPaymentGooglePay' => $controller instanceof OrderController
                && $paymentId === Module::PAYMENT_GOOGLE_PAY_ID,
            'orderPaymentApplePay' => $controller instanceof OrderController
                && $paymentId === Module::PAYMENT_APPLE_PAY_ID,
            'paymentConfigNeedsCard' => $this->paymentMethodsConfigurationNeedsCardField(
                $controller,
                $viewConfig,
                $payment
            ),
            'googlePayConfigurationJson' => json_encode(
                [
                    'amount' => [
                        'currency' => $viewConfig->getAdyenAmountCurrency(),
                        'value' => $viewConfig->getAdyenAmountValue(),
                    ],
                    'countryCode' => $viewConfig->getAdyenCountryIso(),
                    'environment' => $viewConfig->getAdyenOperationMode(),
                    'configuration' => $this->ApiResponsePaymentMethodsService->getGooglePayConfiguration()
                ],
            ),
            'applePayConfigurationJson' => json_encode(
                [
                    'amount' => [
                        'currency' => $viewConfig->getAdyenAmountCurrency(),
                        'value' => $viewConfig->getAdyenAmountValue(),
                    ],
                    'countryCode' => $viewConfig->getAdyenCountryIso(),
                    'configuration' => $this->ApiResponsePaymentMethodsService->getApplePayConfiguration(),
                ],
            ),
            'payPalMerchantId' => $this->moduleSettings->getPayPalMerchantId(),
        ];
    }

    private function getConfigFieldsJsonFormatted(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): string {
        /** @var User $user */
        $user = $controller->getUser();
        $configFieldsArray = $this->configurationService->getConfigFieldsAsArray(
            $viewConfig,
            $user,
            $payment
        );

        $configFieldsJson = json_encode($configFieldsArray);
        if (false === $configFieldsJson) {
            $this->logger->error(
                sprintf(
                    '%s::getDefaultConfigFieldsJsonFormatted error during json_encode `%s`',
                    self::class,
                    var_export($configFieldsArray, true)
                )
            );

            return '';
        }

        // replace leading and ending curly bracket, because, we need to join
        // js function in the resulting js object in adyen_assets_configuration.html.twig
        $configFieldsJsonResult = preg_replace(
            [
                '/^{/',
                '/}$/',
                '/"([^"]+)":/'
            ],
            [
                '',
                '',
                '$1:'
            ],
            $configFieldsJson
        );

        if (is_null($configFieldsJsonResult)) {
            $this->logger->error(
                sprintf(
                    '%s::getDefaultConfigFieldsJsonFormatted error during preg_replace `%s`',
                    self::class,
                    $configFieldsJson
                )
            );

            return '';
        }

        return $configFieldsJsonResult;
    }

    private function paymentMethodsConfigurationNeedsCardField(
        FrontendController $controller,
        ViewConfig $viewConfig,
        ?Payment $payment
    ): bool {
        /** @var AdyenPayment $payment */
        /** @var AdyenViewConfig $viewConfig */
        return $controller instanceof PaymentController
            && $payment instanceof Payment
            && $payment->showInPaymentCtrl()
            && $payment->getId() === $viewConfig->getAdyenPaymentCreditCardId();
    }
}
