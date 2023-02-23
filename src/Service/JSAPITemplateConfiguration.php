<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\EshopCommunity\Application\Controller\FrontendController;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidSolutionCatalysts\Adyen\Controller\OrderController;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Country;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Model\Payment;

class JSAPITemplateConfiguration
{
    private TemplateEngineInterface $templateEngine;

    public function __construct(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function getConfiguration(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): string {
        return $this->templateEngine->render(
            'modules/osc/adyen/payment/adyen_assets_configuration.tpl',
            $this->getViewData($viewConfig, $controller, $payment)
        );
    }

    private function getViewData(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): array {
        return [
            'configFields' => $this->getDefaultConfigFieldsJsonFormatted(
                $viewConfig,
                $controller,
                $payment
            ),
            'isLog' => $viewConfig->isAdyenLoggingActive(),
            'isPaymentPage' => $controller instanceof PaymentController,
            'isOrderPage' => $controller instanceof OrderController,
            'paymentConfigNeedsCard' => $this->paymentMethodsConfigurationNeedsCardField(
                $controller,
                $viewConfig,
                $payment
            ),
        ];
    }

    private function getDefaultConfigFieldsJsonFormatted(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): string {
        $configFieldsArray = [
            'environment' => $viewConfig->getAdyenOperationMode(),
            'clientKey' => $viewConfig->getAdyenClientKey(),
            'analytics' => [
                'enabled' => $viewConfig->isAdyenLoggingActive(),
            ],
            'locale' => $viewConfig->getAdyenShopperLocale(),
            'deliveryAddress' => $this->getAdyenDeliveryAddress($controller),
            'shopperName' => $this->getAdyenShopperName($controller),
            'shopperEmail' => $this->getAdyenShopperEmail($controller),
            'shopperIP' => $viewConfig->getRemoteAddress(),
        ];

        $configFieldsArray = array_merge(
            $configFieldsArray,
            $this->getPaymentPageConfigFields(
                $viewConfig
            ),
            $this->getOrderPageConfigFields(
                $viewConfig,
                $payment
            )
        );

        $configFieldsJson = json_encode($configFieldsArray);

        // replace leading and ending curly bracket, because, we need to join
        // js function in the resulting js object in adyen_assets_configuration.tpl
        return preg_replace(
            [
                '/^\{/',
                '/\}$/',
                '/"([^"]+)":/'
            ],
            [
                '',
                '',
                '$1:'
            ],
            $configFieldsJson
        );
    }

    private function getPaymentPageConfigFields(
        ViewConfig $viewConfig
    ): array {
        return ($viewConfig->getTopActiveClassName() === 'payment') ?
            [
                'paymentMethodsResponse' => $viewConfig->getAdyenPaymentMethods(),
            ] :
            [];
    }

    private function getOrderPageConfigFields(
        ViewConfig $viewConfig,
        ?Payment $payment
    ): array {
        if ($viewConfig->getTopActiveClassName() === 'order') {
            $configFields = [
                'countryCode' => $viewConfig->getAdyenCountryIso(),
                'amount' => [
                    'currency' => $viewConfig->getAdyenAmountCurrency(),
                    'value' => $viewConfig->getAdyenAmountValue(),
                ],
            ];

            if ($payment && $payment->getId() === $viewConfig->getAdyenPaymentPayPalId()) {
                $configFields['merchantId'] = $viewConfig->getAdyenPayPalMerchantId();
            }

            return $configFields;
        }

        return [];
    }

    private function getAdyenShopperName(FrontendController $controller): array
    {
        /** @var User $user */
        $user = $controller->getUser();
        /** @var Address|null $address */
        $address = $user->getSelectedAddress();
        /** @var Address|User $dataObj */
        $dataObj = $address ?: $user;

        return [
            'firstName' => $dataObj->getAdyenStringData('oxfname'),
            'lastName' => $dataObj->getAdyenStringData('oxlname')
        ];
    }

    private function getAdyenDeliveryAddress(FrontendController $controller): array
    {
        /** @var User $user */
        $user = $controller->getUser();
        /** @var Address|null $address */
        $address = $user->getSelectedAddress();
        /** @var Address|User $dataObj */
        $dataObj = $address ?: $user;

        /** @var Country $country */
        $country = oxNew(Country::class);
        $country->load($dataObj->getAdyenStringData('oxcountryid'));
        /** @var null|string $countryIso */
        $countryIso = $country->getAdyenStringData('oxisoalpha2');

        return [
            'city' => $dataObj->getAdyenStringData('oxcity'),
            'country' => $countryIso,
            'houseNumberOrName' => $dataObj->getAdyenStringData('oxstreetnr'),
            'postalCode' => $dataObj->getAdyenStringData('oxzip'),
            'stateOrProvince' => $dataObj->getAdyenStringData('oxstateid'),
            'street' => $dataObj->getAdyenStringData('oxstreet')
        ];
    }

    private function paymentMethodsConfigurationNeedsCardField(
        FrontendController $controller,
        ViewConfig $viewConfig,
        ?Payment $payment
    ): bool {
        return $controller instanceof PaymentController
            && $payment instanceof Payment
            && $payment->showInPaymentCtrl()
            && $payment->getId() === $viewConfig->getAdyenPaymentCreditCardId();
    }

    private function getAdyenShopperEmail(FrontendController $controller): string
    {
        /** @var User $user */
        $user = $controller->getUser();
        return $user->getAdyenStringData('oxusername');
    }
}
