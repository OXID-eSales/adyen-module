<?php

namespace OxidSolutionCatalysts\Adyen\Service;

/**
 * use this service as an alternative to oxid core oxNew, for writing more readable code and better unit tests
 */
class OxNewService
{
    /**
     * @template T
     * @param class-string<T> $fqcn
     * @param array $constructorArgs
     * @return T returns type of fqcn
     */
    public function oxNew($fqcn, array $constructorArgs = [])
    {
        return oxNew($fqcn, ...$constructorArgs);
    }
}
