<?php

namespace OxidSolutionCatalysts\Adyen\Tests\PhpUnitExtensions;

use Dotenv\Dotenv;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitDotEnv implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../tests/');
        $dotenv->load();
    }
}
