<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidEsales\Eshop\Application\Model\Order as eShopOrder;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;

class PaymentGateway
{
    use RequestGetter;

    private SessionSettings $sessionSettings;
    private PaymentGatewayOrderSavable $gatewayOrderSavable;
    private ModuleService $moduleService;

    public function __construct(
        SessionSettings $sessionSettings,
        PaymentGatewayOrderSavable $gatewayOrderSavable,
        ModuleService $moduleService
    ) {
        $this->sessionSettings = $sessionSettings;
        $this->gatewayOrderSavable = $gatewayOrderSavable;
        $this->moduleService = $moduleService;
    }

    public function doFinishAdyenPayment(float $amount, eShopOrder $order): bool
    {
        $success = false;

        $paymentId = $this->sessionSettings->getPaymentId();
        $pspReference = $this->sessionSettings->getPspReference();
        $resultCode = $this->sessionSettings->getResultCode();
        $amountCurrency = $this->sessionSettings->getAmountCurrency();
        $orderReference = $this->sessionSettings->getOrderReference();

        // everything is fine, we can save the references
        if ($this->gatewayOrderSavable->prove($pspReference, $resultCode, $orderReference)) {
            // not necessary anymore, so cleanup
            $this->sessionSettings->deletePaymentSession();

            /** @var AdyenOrder $order */
            $order->setAdyenOrderReference($orderReference);
            $order->setAdyenPSPReference($pspReference);
            $order->setAdyenHistoryEntry(
                $pspReference,
                $pspReference,
                $order->getId(),
                $amount,
                $amountCurrency,
                $resultCode,
                Module::ADYEN_ACTION_AUTHORIZE
            );
            $order->save();

            // trigger Capture for all PaymentCtrl-Payments with Capture-Delay "immediate"
            if ($this->moduleService->showInPaymentCtrl($paymentId)) {
                $order->captureAdyenOrder($amount);
            }

            $success = true;
        }

        return $success;
    }

    /**
     * put RequestData from OrderCtrl in the session as well as from PaymentCtrl
     */
    public function doCollectAdyenRequestData(): void
    {
        $pspReference = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME);
        $resultCode = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_RESULTCODE_NAME);
        $amountCurrency = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME);
        $this->sessionSettings->setPspReference($pspReference);
        $this->sessionSettings->setResultCode($resultCode);
        $this->sessionSettings->setAmountCurrency($amountCurrency);
    }
}
