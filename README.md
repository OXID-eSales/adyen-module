# OXID Solution Catalysts Adyen Module

Adyen integration for OXID eShop 6.5 and above.

## Documentation

* Official German Adyen Payment for OXID [documentation](https://docs.oxid-esales.com/modules/adyen/de/latest/).
* Official English Adyen Payment for OXID [documentation](https://docs.oxid-esales.com/modules/adyen/en/latest/).

## Branch Compatibility

* b-6.5.x module branch is compatible with OXID eShop compilation 6.5

## Install for OXID

* see Official documentation

## Limitations

* tbd

## Running tests

Warning: Running tests will reset the shop.

#### Requirements:
* Ensure test_config.yml is configured:
    * ```
    partial_module_paths: osc/adyen
    ```
    * ```
    activate_all_modules: true
    run_tests_for_shop: false
    run_tests_for_modules: true
    ```
* For codeception tests to be running, selenium server should be available, several options to solve this:
    * Use OXID official [vagrant box environment](https://github.com/OXID-eSales/oxvm_eshop).
    * Use OXID official [docker sdk configuration](https://github.com/OXID-eSales/docker-eshop-sdk).
    * Use other preconfigured containers, example: ``image: 'selenium/standalone-chrome-debug:3.141.59'``

#### Run

Running phpunit tests:
```
vendor/bin/runtests
```

Running phpunit tests with coverage reports (report is generated in ``.../adyen/Tests/reports/`` directory):
```
XDEBUG_MODE=coverage vendor/bin/runtests-coverage
```

Running codeception tests default way (Host: selenium, browser: chrome):
```
vendor/bin/runtests-codeception
```

Running codeception tests example with specific host/browser/testgroup:
```
SELENIUM_SERVER_HOST=seleniumchrome BROWSER_NAME=chrome vendor/bin/runtests-codeception --group=examplegroup
```