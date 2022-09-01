<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidSolutionCatalysts\Adyen\Service\Repository;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\TestingLibrary\UnitTestCase;

final class RepositoryTest extends UnitTestCase
{
    use ServiceContainer;
}
