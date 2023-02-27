<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Traits\UserAddress;
use OxidSolutionCatalysts\Adyen\Service\Payment as PaymentService;

class PaymentController extends PaymentController_parent
{
    use ServiceContainer;
    use UserAddress;
    use RequestGetter;

    protected ?bool $assetsNecessary = null;

    /**
     * Template variable getter. Returns paymentlist
     *
     * @return array<array-key, mixed>|object
     */
    public function getPaymentList()
    {
        /** @var array $paymentListRaw */
        $paymentListRaw = parent::getPaymentList();
        $adyenDef = Module::PAYMENT_DEFINTIONS;
        $actShopCurrency = Registry::getConfig()->getActShopCurrencyObject();
        $countryRepository = $this->getServiceFromContainer(CountryRepository::class);
        $userCountryIso = $countryRepository->getCountryIso();

        $paymentList = [];

        $paymentService = $this->getServiceFromContainer(PaymentService::class);
        $paymentListRaw = $paymentService->filterNonSupportedCurrencies($paymentListRaw, $actShopCurrency->name);

        $adyenHealth = $this->getServiceFromContainer(ModuleSettings::class)->checkConfigHealth();

        /*
         * check & allow:
         * - all none Adyen-Payments
         * - adyenHealth
         * - currency
         * - country
         */
        foreach ($paymentListRaw as $key => $payment) {
            if (
                !isset($adyenDef[$key]) ||
                (
                    $adyenHealth &&
                    (
                        empty($adyenDef[$key]['currencies']) || // @phpstan-ignore-line
                        in_array($actShopCurrency->name, $adyenDef[$key]['currencies'], true)
                    ) &&
                    (
                        empty($adyenDef[$key]['countries']) || // @phpstan-ignore-line
                        in_array($userCountryIso, $adyenDef[$key]['countries'], true)
                    )
                )
            ) {
                $paymentList[$key] = $payment;
            }
        }
        return $paymentList;
    }

    public function isAdyenAssetsNecessary(): bool
    {
        if (is_null($this->assetsNecessary)) {
            $this->assetsNecessary = false;
            $paymentList = $this->getPaymentList();
            if (is_array($paymentList)) {
                /** @var Payment $paymentObj */
                foreach ($paymentList as $paymentObj) {
                    if ($paymentObj->showInPaymentCtrl()) {
                        $this->assetsNecessary = true;
                        break;
                    }
                }
            }
            $this->assetsNecessary = $this->assetsNecessary && !$this->isValidAdyenAuthorisation();
        }
        return $this->assetsNecessary;
    }

    public function isActiveAdyenSession(): bool
    {
        /** @var SessionSettings $session */
        $session = $this->getServiceFromContainer(SessionSettings::class);
        return $session->getPspReference() !== '';
    }

    public function isValidAdyenAuthorisation(): bool
    {
        /** @var SessionSettings $session */
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $validSessionAmount = $session->getAdyenBasketAmount() <= $session->getAmountValue();
        return $this->isActiveAdyenSession() && $validSessionAmount;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentError(): ?string
    {
        return (
            $this->isActiveAdyenSession() && !$this->isValidAdyenAuthorisation() ?
            Module::ADYEN_ERROR_INVALIDSESSION_NAME :
            parent::getPaymentError()
        );
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return  mixed
     */
    public function validatePayment()
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $moduleService = $this->getServiceFromContainer(ModuleService::class);
        $actualPaymentId = $session->getPaymentId();
        $newPaymentId = $this->getStringRequestData('paymentid');
        $pspReference = $session->getPspReference();

        // remove a possible old adyen payment if another one was selected
        if (
            $actualPaymentId &&
            $actualPaymentId !== $newPaymentId &&
            $moduleService->isAdyenPayment($actualPaymentId)
        ) {
            $this->removeAdyenPaymentFromSession();
        }

        $result = parent::validatePayment();

        // collect the paymentId again, because it may have changed in the meantime
        $actualPaymentId = $session->getPaymentId();
        if (
            $actualPaymentId &&
            !$pspReference &&
            $moduleService->isAdyenPayment($actualPaymentId)
        ) {
            $this->saveAdyenPaymentInSession();
        }

        return $result;
    }

    public function isAvailablePayment(string $paymentIdToProve): bool
    {
        $paymentList = $this->getPaymentList();
        foreach ($paymentList as $paymentId => $payment) {
            if ($payment->showInPaymentCtrl() && $paymentId === $paymentIdToProve) {
                return true;
            }
        }

        return false;
    }

    protected function saveAdyenPaymentInSession(): void
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $pspReference = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME);
        $resultCode = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_RESULTCODE_NAME);
        $amountCurrency = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME);
        $amountValue = $this->getFloatRequestData(Module::ADYEN_HTMLPARAM_AMOUNTVALUE_NAME);
        $session->setPspReference($pspReference);
        $session->setResultCode($resultCode);
        $session->setAmountCurrency($amountCurrency);
        $session->setAmountValue($amountValue);
    }

    protected function removeAdyenPaymentFromSession(): void
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);

        // cancel authorization
        $pspReference = $session->getPspReference();
        $reference = $session->getOrderReference();
        if ($pspReference && $reference) {
            $paymentService = $this->getServiceFromContainer(PaymentCancel::class);
            $paymentService->doAdyenCancel(
                $pspReference,
                $reference
            );
            $session->deletePaymentSession();
        }
    }
}
