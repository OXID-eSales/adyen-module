<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use Dotenv\Dotenv;

trait Env
{
    public function getEnv()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }
}
