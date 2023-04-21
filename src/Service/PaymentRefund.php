<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIRefunds;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * @extendable-class
 */
class PaymentRefund extends PaymentBase
{
    use AdyenPayment;

    private array $refundResult = [];

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponseRefunds $APIRefunds;

    private OxNewService $oxNewService;

    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponseRefunds $APIRefunds,
        OxNewService $oxNewsService
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIRefunds = $APIRefunds;
        $this->oxNewService = $oxNewsService;
    }

    public function setRefundResult(array $refundResult): void
    {
        $this->refundResult = $refundResult;
    }

    public function getRefundResult(): array
    {
        return $this->refundResult;
    }

    /**
     * @param double $amount Goods amount
     * @param string $pspReference User ordering object
     * @param string $orderNr as unique reference
     */
    public function doAdyenRefund(float $amount, string $pspReference, string $orderNr): bool
    {
        $result = false;

        $refunds = $this->oxNewService->oxNew(AdyenAPIRefunds::class);
        $refunds->setCurrencyName($this->context->getActiveCurrencyName());
        $refunds->setReference($orderNr);
        $refunds->setPspReference($pspReference);
        $refunds->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $refunds->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $refunds->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $refunds->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);
        $refunds->setPlatformName(Module::MODULE_PLATFORM_NAME);
        $refunds->setPlatformVersion(Module::MODULE_PLATFORM_VERSION);
        $refunds->setPlatformIntegrator(Module::MODULE_PLATFORM_INTEGRATOR);

        try {
            $resultRefund = $this->APIRefunds->setRefund($refunds);
            if (is_array($resultRefund)) {
                $this->setRefundResult($resultRefund);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on setRefund call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }
}
