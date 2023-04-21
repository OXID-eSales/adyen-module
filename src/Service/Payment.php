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
use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidEsales\Eshop\Application\Model\User;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidEsales\Eshop\Application\Model\Payment as PaymentModel;
use OxidSolutionCatalysts\Adyen\Controller\OrderController;

/**
 * @extendable-class
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) is too high, hard to refactor
 */
class Payment extends PaymentBase
{
    use AdyenPayment;

    private array $paymentResult = [];

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponsePayments $APIPayments;
    private CountryRepository $countryRepository;
    private AdyenAPILineItemsService $adyenAPILineItemsService;
    private SessionSettings $sessionSettings;
    private OxNewService $oxNewService;

    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePayments $APIPayments,
        CountryRepository $countryRepository,
        AdyenAPILineItemsService $adyenAPILineItemsService,
        SessionSettings $sessionSettings,
        OxNewService $oxNewService
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIPayments = $APIPayments;
        $this->countryRepository = $countryRepository;
        $this->adyenAPILineItemsService = $adyenAPILineItemsService;
        $this->sessionSettings = $sessionSettings;
        $this->oxNewService = $oxNewService;
    }

    public function setPaymentResult(array $paymentResult): void
    {
        $this->paymentResult = $paymentResult;
    }

    public function getPaymentResult(): array
    {
        return $this->paymentResult;
    }

    /**
     * @param double $amount Goods amount
     * @param string $reference Unique Order-Reference
     * @param array $paymentState
     */
    public function collectPayments(
        float $amount,
        string $reference,
        array $paymentState,
        User $user,
        ViewConfig $viewConfig
    ): bool {
        $result = false;

        $payments = $this->oxNewService->oxNew(AdyenAPIPayments::class);
        $payments->setCurrencyName($this->context->getActiveCurrencyName());
        $payments->setReference($reference);
        $payments->setPaymentMethod($paymentState['paymentMethod'] ?? []);
        $payments->setOrigin($paymentState['origin'] ?? '');
        $payments->setBrowserInfo($paymentState['browserInfo'] ?? []);
        $payments->setShopperEmail($paymentState['shopperEmail'] ?? $user->getAdyenStringData('oxusername'));
        $payments->setShopperIP($paymentState['shopperIP'] ?? $viewConfig->getRemoteAddress());
        $payments->setShopperReference($user->getId());
        $payments->setShopperCountryCode($this->countryRepository->getCountryIso());
        $payments->setLineItems($this->adyenAPILineItemsService->getLineItems());
        $payments->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $payments->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $payments->setReturnUrl(
            $this->context->getPaymentReturnUrl(
                $viewConfig->getSessionChallengeToken(),
                $this->oxNewService->oxNew(OrderController::class)->getDeliveryAddressMD5(),
                $this->sessionSettings->getPspReference(),
                $this->sessionSettings->getResultCode(),
                $this->sessionSettings->getAmountCurrency()
            )
        );
        $payments->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $payments->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);
        $payments->setPlatformName(Module::MODULE_PLATFORM_NAME);
        $payments->setPlatformVersion(Module::MODULE_PLATFORM_VERSION);
        $payments->setPlatformIntegrator(Module::MODULE_PLATFORM_INTEGRATOR);

        try {
            $resultPayments = $this->APIPayments->getPayments($payments);
            if (is_array($resultPayments)) {
                $this->setPaymentResult($resultPayments);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getPayments call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }

    public function supportsCurrency(string $currency, string $paymentId): bool
    {
        // no supported_currencies field means all currency are supported
        if (!isset(ModuleCore::PAYMENT_DEFINTIONS[$paymentId]['supported_currencies'])) {
            return true;
        }

        return in_array($currency, ModuleCore::PAYMENT_DEFINTIONS[$paymentId]['supported_currencies']);
    }

    public function filterNonSupportedCurrencies(array &$payments, string $actualCurrency): array
    {
        return array_filter(
            $payments,
            function (PaymentModel $payment) use ($actualCurrency) {
                return $this->supportsCurrency($actualCurrency, $payment->getId());
            }
        );
    }

    public function filterNoSpecialMerchantId(array &$payments): array
    {
        return array_filter(
            $payments,
            function (PaymentModel $payment) {
                return !$this->isPayPalAndNoMerchantId($payment);
            }
        );
    }

    protected function isPayPalAndNoMerchantId(PaymentModel $payment): bool
    {
        return $payment->getId() === Module::PAYMENT_PAYPAL_ID
            && empty($this->moduleSettings->getPayPalMerchantId());
    }
}
