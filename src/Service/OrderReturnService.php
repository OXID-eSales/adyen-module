<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Registry;

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
}
