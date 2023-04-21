<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception;

use Exception;

class EnvLoader
{
    /**
     * @throws Exception
     */
    public function getEnvVar(string $envKey): string
    {
        if (empty($_ENV[$envKey])) {
            throw new Exception(
                sprintf(
                    'the env variable %s is not setup, please configure it in the tests/.env'
                    . ', have a look at the .env.example',
                    $envKey
                )
            );
        }

        return $_ENV[$envKey];
    }
}
