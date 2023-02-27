<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\EshopCommunity\Application\Controller\FrontendController;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Country;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Model\Payment;

class JSAPIConfigurationService
{
    private AdyenAPILineItemsService $lineItemsService;

    public function __construct(
        AdyenAPILineItemsService $lineItemsService
    ) {
        $this->lineItemsService = $lineItemsService;
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
                'enabled' => $viewConfig->isAdyenLoggingActive(),
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
        if ($viewConfig->getTopActiveClassName() === 'order') {
            $configFields = [
                'countryCode' => $viewConfig->getAdyenCountryIso(),
                'amount' => [
                    'currency' => $viewConfig->getAdyenAmountCurrency(),
                    'value' => $viewConfig->getAdyenAmountValue(),
                ],
                'lineItems' => $this->lineItemsService->getLineItems(),
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
