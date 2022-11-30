<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig as eShopViewConfig;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;

class ViewConfigTest extends UnitTestCase
{
    public function testForwardedModuleConstants(): void
    {
        $viewConfig = Registry::get(eShopViewConfig::class);
        $this->assertSame($viewConfig->getAdyenSDKVersion(), Module::ADYEN_SDK_VERSION);
        $this->assertSame($viewConfig->getAdyenIntegrityJS(), Module::ADYEN_INTEGRITY_JS);
        $this->assertSame($viewConfig->getAdyenIntegrityCSS(), Module::ADYEN_INTEGRITY_CSS);
        $this->assertStringContainsString(
            'index.php?cl=AdyenWebhookController',
            $viewConfig->getWebhookControllerUrl()
        );
    }
}
