<?php

namespace OxidSolutionCatalysts\Adyen\Traits;

trait ParentMethodStubableTrait
{
    /**
     * use this method to call parent methods and be able
     * to stub this call in a test
     *
     * @param string $method
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function parentCall(string $method, ...$args)
    {
        return parent::$method(...$args);
    }
}
