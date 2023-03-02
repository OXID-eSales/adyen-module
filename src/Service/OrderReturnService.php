<?php

namespace OxidSolutionCatalysts\Adyen\Service;

/**
 * service for use case when shopper came back from adyen
 */
class OrderReturnService
{
    private AdyenAPIResponsePaymentDetails $apiResponsePaymentDetails;
    private array $paymentDetailCache = [];

    public function __construct(AdyenAPIResponsePaymentDetails $apiResponsePaymentDetails)
    {
        $this->apiResponsePaymentDetails = $apiResponsePaymentDetails;
    }

    public function isRedirectedFromAdyen(): bool
    {
        $redirectResult = $_GET['redirectResult'] ?? '';
        $controller = $_GET['cl'] ?? '';
        $function = $_GET['fnc'] ?? '';

        return !empty($redirectResult)
            && $controller === 'order'
            && $function === 'return';
    }

    public function getPaymentDetails(): array
    {
        $redirectResult = $_GET['redirectResult'] ?? '';
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
}
