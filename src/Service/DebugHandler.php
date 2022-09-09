<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Service;

use Monolog\Logger;

class DebugHandler
{
    /** @var Logger */
    protected $logger;

    /**
     * @param Logger $moduleLogger
     */
    public function __construct(Logger $moduleLogger)
    {
        $this->logger = $moduleLogger;
    }
}
