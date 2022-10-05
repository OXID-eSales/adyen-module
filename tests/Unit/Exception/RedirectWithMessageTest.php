<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Exception;

use OxidSolutionCatalysts\Adyen\Exception\RedirectWithMessage;
use PHPUnit\Framework\TestCase;

class RedirectWithMessageTest extends TestCase
{
    public function testIsThrowable(): void
    {
        $sut = new RedirectWithMessage('x', 'y');
        $this->assertInstanceOf(\Throwable::class, $sut);
    }

    public function testBasicGetters(): void
    {
        $destination = 'redirectDirection';
        $messageKey = 'messageKey';
        $messageParams = ['param1', 'param2'];
        $sut = new RedirectWithMessage($destination, $messageKey, $messageParams);

        $this->assertSame($destination, $sut->getDestination());
        $this->assertSame($messageKey, $sut->getMessageKey());
        $this->assertSame($messageParams, $sut->getMessageParams());
    }

    public function testGetDefaultMessageParams(): void
    {
        $sut = new RedirectWithMessage('x', 'y');

        $this->assertSame([], $sut->getMessageParams());
    }
}
