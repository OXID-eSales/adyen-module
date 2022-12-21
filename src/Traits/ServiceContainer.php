<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;

/**
 * Convenience trait to fetch services from DI container.
 * Use for example in classes where it's not possible to inject services in
 * the constructor because constructor is inherited from a shop core class.
 */
trait ServiceContainer
{
    protected function getServiceFromContainer(string $serviceName)
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get($serviceName);
    }
}
