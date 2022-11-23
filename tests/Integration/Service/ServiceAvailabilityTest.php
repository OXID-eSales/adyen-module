<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponse;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCancels;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePayments;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseRefunds;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\Repository;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
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
            [AdyenSDKLoader::class],
            [Context::class],
            [ModuleSettings::class],
            [AdyenAPIResponse::class],
            [AdyenAPIResponseCancels::class],
            [AdyenAPIResponseCaptures::class],
            [AdyenAPIResponseCaptures::class],
            [AdyenAPIResponsePaymentMethods::class],
            [AdyenAPIResponsePayments::class],
            [AdyenAPIResponseRefunds::class],
            [Repository::class],
            [StaticContents::class],
            [UserRepository::class],
            [ResponseHandler::class]
        ];
    }
}
