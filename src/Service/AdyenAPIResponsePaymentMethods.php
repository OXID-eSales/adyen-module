<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * @extendable-class
 */
class AdyenAPIResponsePaymentMethods extends AdyenAPIResponse
{
    use ServiceContainer;

    public const PAYMENT_TYPE_APPLE = 'applepay';
    /**
     * @throws Exception
     */
    public function getAdyenPaymentMethods(): array
    {
        $adyenPaymentMethods = $this->session->getPaymentMethods();
        if (!count($adyenPaymentMethods)) {
            throw new AdyenException('Load the paymentMethods before getting the paymentMethods');
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
            $resultApi = is_array($resultApi) ? $resultApi : [];
            $result = $this->saveAdyenPaymentMethods($resultApi);
            if (!$result) {
                throw new Exception('paymentMethodsData not found in Adyen-Response');
            }
        } catch (AdyenException | Exception $exception) {
            Registry::getLogger()->error($exception->getMessage());
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
        $paymentMethods = $resultApi['paymentMethods'] ? $resultApi : [];
        $result = (bool)$paymentMethods;
        $this->session->setPaymentMethods($paymentMethods);
        return $result;
    }

    public function deleteAdyenPaymentMethods(): void
    {
        $this->session->deletePaymentMethods();
    }

    public function getGooglePayConfiguration(): array
    {
        $moduleSettingsService = $this->getServiceFromContainer(ModuleSettings::class);

        return [
            'gatewayMerchantId' => $moduleSettingsService->getMerchantAccount(),
            'merchantId' => $moduleSettingsService->getGooglePayMerchantId(),
        ];
    }

    public function getApplePayConfiguration(): ?array
    {
        $paymentMethods = $this->getAdyenPaymentMethods();
        $applePayPaymentMethod = $this->getPaymentMethodByType(
            $paymentMethods['paymentMethods'] ?? [],
            self::PAYMENT_TYPE_APPLE
        );
        if (is_array($applePayPaymentMethod)) {
            return $applePayPaymentMethod['configuration'] ?? null;
        }

        return null;
    }

    public function getPaymentMethodByType(array $paymentMethods, string $paymentTypeToFind): ?array
    {
        $foundPaymentMethod = current(
            array_filter(
                $paymentMethods,
                function ($paymentMethod) use ($paymentTypeToFind) {
                    return ($paymentMethod['type'] ?? '') === $paymentTypeToFind;
                }
            )
        );

        return $foundPaymentMethod === false ? null : $foundPaymentMethod;
    }
}
