<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use Monolog\Logger;
use OxidSolutionCatalysts\Adyen\Service\LoggingHandler;
use PHPUnit\Framework\TestCase;

class LoggingHandlerTest extends TestCase
{
    public function testLoggerAvailable(): void
    {
        $testMessage = 'someMessage';

        $loggerMock = $this->createPartialMock(Logger::class, ['info']);
        $loggerMock->expects($this->once())->method('info')->with($testMessage);

        $loggerMock = $this->createPartialMock(Logger::class, ['debug']);
        $loggerMock->expects($this->once())->method('debug')->with($testMessage);

        $loggerMock = $this->createPartialMock(Logger::class, ['alert']);
        $loggerMock->expects($this->once())->method('alert')->with($testMessage);

        $loggerMock = $this->createPartialMock(Logger::class, ['warning']);
        $loggerMock->expects($this->once())->method('warning')->with($testMessage);

        $loggerMock = $this->createPartialMock(Logger::class, ['notice']);
        $loggerMock->expects($this->once())->method('notice')->with($testMessage);

        $loggerMock = $this->createPartialMock(Logger::class, ['critical']);
        $loggerMock->expects($this->once())->method('critical')->with($testMessage);

        $loggerMock = $this->createPartialMock(Logger::class, ['log']);
        $loggerMock->expects($this->once())->method('log')->with('info', $testMessage);

        $sut = new LoggingHandler($loggerMock);
        $sut->log($testMessage);
    }
}
