<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;

class JSAPIConfigurationService
{
    private AdyenAPILineItemsService $lineItemsService;
    private UserAddress $userAddressService;

    public function __construct(
        AdyenAPILineItemsService $lineItemsService,
        UserAddress              $userAddressService
    ) {
        $this->lineItemsService = $lineItemsService;
        $this->userAddressService = $userAddressService;
    }

    public function getConfigFieldsAsArray(
        ViewConfig $viewConfig,
        User       $user,
        ?Payment   $payment
    ): array {
        $configFieldsArray = [
            'environment' => $viewConfig->getAdyenOperationMode(),
            'clientKey' => $viewConfig->getAdyenClientKey(),
            'analytics' => [
                'enabled' => $viewConfig->isAdyenAnalyticsActive(),
            ],
            'locale' => $viewConfig->getAdyenShopperLocale(),
            'deliveryAddress' => $this->userAddressService->getAdyenDeliveryAddress($user),
            'shopperName' => $this->userAddressService->getAdyenShopperName($user),
            'shopperEmail' => $this->userAddressService->getAdyenShopperEmail($user),
            'shopperReference' => $user->getId(),
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
            ),
            $this->extendPayButtonConfigFields($payment)
        );

        return $configFieldsArray;
    }

    /**
     * workaround due to https://oxid-esales.atlassian.net/browse/AM-58?focusedCommentId=154815
     */
    private function extendPayButtonConfigFields(?Payment $payment): array
    {
        $configFields = ['showPayButton' => true];
        $paymentId = $payment instanceof Payment ? $payment->getId() : '';
        if ($paymentId === Module::PAYMENT_TWINT_ID) {
            $configFields['name'] = 'Twint';
        }

        return $configFields;
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
        ?Payment   $payment
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
}
