<?php

# this php is included by vendor/oxid-esales/testing-library/library/Bootstrap/UnitBootstrap.php

use Dotenv\Dotenv;

# load .env parameters for the tests
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
