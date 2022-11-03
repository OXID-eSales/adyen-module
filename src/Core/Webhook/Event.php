<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

final class Event
{
    /**
     * @var array
     */
    private $data;

    /**
     * Event constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get event data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
