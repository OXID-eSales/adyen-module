<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * service for use case when shopper came back from adyen
 */
class OrderReturnService
{
    use AdyenPayment;

    private AdyenAPIResponsePaymentDetails $apiResponsePaymentDetails;
    private array $paymentDetailCache = [];

    public function __construct(AdyenAPIResponsePaymentDetails $apiResponsePaymentDetails)
    {
        $this->apiResponsePaymentDetails = $apiResponsePaymentDetails;
    }

    public function isRedirectedFromAdyen(): bool
    {
        $request = Registry::getRequest();
        $redirectResult = (string)$request->getRequestParameter('redirectResult');
        $controller = (string)$request->getRequestParameter('cl');
        $function = (string)$request->getRequestParameter('fnc');

        return !empty($redirectResult)
            && $controller === 'order'
            && $function === 'return';
    }

    public function getPaymentDetails(): array
    {
        $redirectResult = (string)Registry::getRequest()->getRequestParameter('redirectResult');
        $cacheKey = $this->getCacheKey($redirectResult);

        if (!array_key_exists($cacheKey, $this->paymentDetailCache)) {
            $this->paymentDetailCache[$cacheKey] = $this->apiResponsePaymentDetails->getPaymentDetails(
                ['details' => ['redirectResult' => $redirectResult]]
            );
        }

        return $this->paymentDetailCache[$cacheKey];
    }

    private function getCacheKey(string $redirectResult): string
    {
        return sha1($redirectResult);
    }

    /**
     * Check whether Adyen authorized at least the given order total (bug 0007976).
     *
     * When the shopper changes the basket in a parallel tab/session after the Adyen
     * redirect was started, Adyen may authorize less than the current order total.
     * Comparison is done in integer minor units (currency-aware) to avoid float noise.
     * If the redirect response carries no amount, or the total is not positive, we
     * cannot compare and return true (do not block) to avoid false positives.
     */
    public function isAuthorizedAmountSufficient(array $paymentDetails, float $orderTotal): bool
    {
        if (!isset($paymentDetails['amount']['value']) || $orderTotal <= 0.0) {
            return true;
        }

        $currency = (string)($paymentDetails['amount']['currency'] ?? '');
        $currencyDecimals = $this->getCurrencyDecimalsByName($currency);

        $authorizedMinor = (int)$paymentDetails['amount']['value'];
        $orderTotalMinor = (int)$this->getAdyenAmount($orderTotal, $currencyDecimals);

        return $authorizedMinor >= $orderTotalMinor;
    }
}
