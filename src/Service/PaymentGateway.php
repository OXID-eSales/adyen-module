<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;
use OxidEsales\Eshop\Application\Model\Payment;
use stdClass;

class PaymentGateway
{
    use RequestGetter;
    use AdyenPayment;

    private SessionSettings $sessionSettings;
    private PaymentGatewayOrderSavable $gatewayOrderSavable;
    private PaymentConfigService $paymentConfigService;
    private OrderReturnService $orderRedirectService;
    private OxNewService $oxNewService;

    public function __construct(
        SessionSettings $sessionSettings,
        PaymentGatewayOrderSavable $gatewayOrderSavable,
        PaymentConfigService $paymentConfigService,
        OrderReturnService $orderRedirectService,
        OxNewService $oxNewService
    ) {
        $this->sessionSettings = $sessionSettings;
        $this->gatewayOrderSavable = $gatewayOrderSavable;
        $this->paymentConfigService = $paymentConfigService;
        $this->orderRedirectService = $orderRedirectService;
        $this->oxNewService = $oxNewService;
    }

    public function doFinishAdyenPayment(float $amount, Order $order): bool
    {
        $success = false;

        $paymentId = $this->sessionSettings->getPaymentId();
        $pspReference = $this->sessionSettings->getPspReference();
        $resultCode = $this->sessionSettings->getResultCode();
        $amountCurrency = $this->getOrderCurrencyName($order);
        $orderReference = $this->sessionSettings->getOrderReference();

        // Amount actually authorized by Adyen. Falls back to the order total for flows
        // where Adyen does not report a dedicated authorized amount (e.g. the in-page
        // PaymentCtrl flow); overridden below with the value Adyen returns on redirect.
        $authorizedAmount = $amount;

        $canSave = $this->gatewayOrderSavable->prove($pspReference, $resultCode, $orderReference);

        if (!$canSave && $this->orderRedirectService->isRedirectedFromAdyen()) {
            $paymentDetails = $this->orderRedirectService->getPaymentDetails();
            $resultCode = $paymentDetails['resultCode'];
            $pspReference = $paymentDetails['pspReference'];
            $orderReference = $paymentDetails['merchantReference'];
            $amountCurrency = $paymentDetails['amount']['currency'] ?? $amountCurrency;

            // Use the amount Adyen actually authorized instead of the current order total.
            // When the basket is modified in a parallel tab/session after the Adyen
            // redirect was started, the order total can exceed what Adyen authorized;
            // storing the order total would misreport the order as fully authorized and
            // lead to an over-capture later (critical with delayed/two-step capture, e.g.
            // Klarna, where the discrepancy only surfaces at capture time). See bug 0007976.
            // The value comes in Adyen minor units and is converted currency-aware.
            if (isset($paymentDetails['amount']['value'])) {
                $authorizedAmount = $this->getOxidAmount(
                    (float)$paymentDetails['amount']['value'],
                    $this->getCurrencyDecimalsByName($amountCurrency)
                );
            }

            $canSave = true;
        }

        // everything is fine, we can save the references
        if ($canSave) {
            // not necessary anymore, so cleanup
            $this->sessionSettings->deletePaymentSession();

            /** @var AdyenOrder $order */
            $order->setAdyenOrderReference($orderReference);
            $order->setAdyenPSPReference($pspReference);
            $order->setAdyenHistoryEntry(
                $pspReference,
                $pspReference,
                $order->getId(),
                $authorizedAmount,
                $amountCurrency,
                $resultCode,
                Module::ADYEN_ACTION_AUTHORIZE
            );
            $order->save();

            // trigger Capture for all Payments with Capture-Delay "immediate".
            // Capture the authorized amount, never more than Adyen authorized (captureAdyenOrder
            // additionally caps it to the remaining capturable order sum).
            if ($this->paymentConfigService->isAdyenImmediateCapture($paymentId)) {
                $order->captureAdyenOrder($authorizedAmount);
            }

            $success = true;
        }

        return $success;
    }

    /**
     * put RequestData from OrderCtrl in the session
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

    protected function getPayment(string $paymentId): Payment
    {
        $payment = $this->oxNewService->oxNew(Payment::class);
        $payment->setId($paymentId);

        return $payment;
    }

    private function getOrderCurrencyName(Order $order): string
    {
        /** @var stdClass $currency */
        $currency = $order->getOrderCurrency();

        return $currency->name;
    }
}
