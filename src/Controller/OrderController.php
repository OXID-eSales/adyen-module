<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\TranslationMapper;
use OxidSolutionCatalysts\Adyen\Service\OrderReturnService;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Traits\UserAddress;
use OxidSolutionCatalysts\Adyen\Core\Module;

class OrderController extends OrderController_parent
{
    use UserAddress;
    use ServiceContainer;

    /**
     * shopper came back from adyen, because of authorization, cancellation, error or refused
     */
    public function return(): ?string
    {
        $orderReturnService = $this->getServiceFromContainer(OrderReturnService::class);

        if (!$orderReturnService->isRedirectedFromAdyen()) {
            Registry::getUtils()->redirect(Registry::getConfig()->getShopHomeUrl() . 'cl=start');
        }

        if ($orderReturnService->isRedirectedFromAdyen()) {
            $paymentDetail = $orderReturnService->getPaymentDetails();
            if (
                $paymentDetail['resultCode'] === Module::ADYEN_RETURN_RESULT_CODE_AUTHORISED
                || $paymentDetail['resultCode'] === Module::ADYEN_RETURN_RESULT_CODE_RECEIVED
            ) {
                return $this->execute();
            }

            $translationMapper = $this->getServiceFromContainer(TranslationMapper::class);
            $this->addTplParam(
                'paymentReturnReason',
                $translationMapper->mapReturnResultCode($paymentDetail['resultCode'])
            );
            $this->addTplParam('paymentResultCode', $paymentDetail['resultCode']);
        }

        return null;
    }
}
