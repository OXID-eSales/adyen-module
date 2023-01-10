<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponse;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseAdjustAuthorisation;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCancels;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentDetails;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePayments;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseRefunds;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\PaymentCapture;
use OxidSolutionCatalysts\Adyen\Service\PaymentDetails;
use OxidSolutionCatalysts\Adyen\Service\PaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\PaymentRefund;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Service\StaticContents;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use PHPUnit\Framework\TestCase;

class ServiceAvailabilityTest extends TestCase
{
    /**
     * @dataProvider serviceAvailabilityDataProvider
     */
    public function testServiceAvailable($service): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $this->assertInstanceOf($service, $container->get($service));
    }

    public static function serviceAvailabilityDataProvider(): array
    {
        return [
            [AdyenAPIResponse::class],
            [AdyenAPIResponseAdjustAuthorisation::class],
            [AdyenAPIResponseCancels::class],
            [AdyenAPIResponseCaptures::class],
            [AdyenAPIResponsePaymentDetails::class],
            [AdyenAPIResponsePaymentMethods::class],
            [AdyenAPIResponsePayments::class],
            [AdyenAPIResponseRefunds::class],
            [AdyenSDKLoader::class],
            [Context::class],
            [CountryRepository::class],
            [ModuleSettings::class],
            [Payment::class],
            [PaymentCancel::class],
            [PaymentCapture::class],
            [PaymentDetails::class],
            [PaymentMethods::class],
            [PaymentRefund::class],
            [ResponseHandler::class],
            [SessionSettings::class],
            [StaticContents::class],
            [UserRepository::class]
        ];
    }
}
