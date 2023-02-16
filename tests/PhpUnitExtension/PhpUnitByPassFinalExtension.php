<?php

namespace OxidSolutionCatalysts\Adyen\Tests\PhpUnitExtension;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeFirstTestHook;

class PhpUnitByPassFinalExtension implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        BypassFinals::enable();
    }
}
