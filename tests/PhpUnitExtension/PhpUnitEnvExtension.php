<?php

namespace OxidSolutionCatalysts\Adyen\Tests\PhpUnitExtension;

use Dotenv\Dotenv;
use PHPUnit\Runner\BeforeFirstTestHook;

class PhpUnitEnvExtension implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../tests/');
        $dotenv->load();
    }
}
