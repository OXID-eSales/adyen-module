<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\EshopCommunity\Application\Controller\FrontendController;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Country;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;

class JSAPIConfigurationService
{
    private AdyenAPILineItemsService $lineItemsService;
    private ModuleSettings $moduleSettings;

    public function __construct(
        AdyenAPILineItemsService $lineItemsService,
        ModuleSettings $moduleSettings
    ) {
        $this->lineItemsService = $lineItemsService;
        $this->moduleSettings = $moduleSettings;
    }

    public function getConfigFieldsAsArray(
        ViewConfig $viewConfig,
        FrontendController $controller,
        ?Payment $payment
    ): array {
        $configFieldsArray = [
            'environment' => $viewConfig->getAdyenOperationMode(),
            'clientKey' => $viewConfig->getAdyenClientKey(),
            'analytics' => [
                'enabled' => $viewConfig->isAdyenAnalyticsActive(),
            ],
            'locale' => $viewConfig->getAdyenShopperLocale(),
            'deliveryAddress' => $this->getAdyenDeliveryAddress($controller),
            'shopperName' => $this->getAdyenShopperName($controller),
            'shopperEmail' => $this->getAdyenShopperEmail($controller),
            'shopperReference' => $this->getAdyenShopperReference($controller),
            'shopperIP' => $viewConfig->getRemoteAddress(),
            'showPayButton' => true,
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

        return $configFieldsArray;
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
        $paymentId = $payment instanceof Payment ? $payment->getId() : '';
        if ($viewConfig->getTopActiveClassName() === 'order') {
            $configFields = [
                'countryCode' => $viewConfig->getAdyenCountryIso(),
                'amount' => [
                    'currency' => $viewConfig->getAdyenAmountCurrency(),
                    'value' => $viewConfig->getAdyenAmountValue(),
                ],
                'lineItems' => $this->lineItemsService->getLineItems($paymentId),
            ];

            if ($paymentId === Module::PAYMENT_PAYPAL_ID) {
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

    private function getAdyenShopperEmail(FrontendController $controller): string
    {
        /** @var User $user */
        $user = $controller->getUser();

        return $user->getAdyenStringData('oxusername');
    }

    private function getAdyenShopperReference(FrontendController $controller): string
    {
        /** @var User $user */
        $user = $controller->getUser();

        return $user->getId();
    }
}
