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
```
    partial_module_paths: osc/adyen
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

## Apple Pay (Dev) Integration
since the Apple Pay integration is the most complex one, here are a few hints
### Sandbox Tester Account and Test Credit Card
- its mandatory needed to have an Apple developer account
- create an Apple sandbox tester account: https://developer.apple.com/apple-pay/sandbox-testing/
- make sure this tester account is not used on any Apple device
- login with you sandbox tester account on your test Apple device
- add a test credit card number to the wallet on that test Apple device
  - had some trouble adding a working test card, this one worked for me: 
    - 4123 4000 7332 0224 
    - Expiration Date: 12/2025 
    - CVV: 863
### Adyen Setup
- the shops webserver need to serve a domain association file: https://docs.adyen.com/payment-methods/apple-pay/web-drop-in?utm_source=ca_test#going-live
  - Download and unzip the domain association file
  - put it reachable under /.well-known/apple-developer-merchantid-domain-association on your shops webserver
  - in my dev setup I experienced problems with Content-Type: text/plain in the header
    - I used the cloudflare ssh tunnel to make my local webserver publicly reachable
    - the cloudflare proxy did not set the Content-Type: text/plain header
    - using a dyndns service to make my local webserver reachable worked for me to solve this
- under Home => Add Payment Methods click "Add more"
  - click "Request Payment Method"
  - search for Apple
  - click checkbox
  - click Adyen's Certificate
  - enter prefered Merchant Name
  - under Shop websites enter the domain under your shop webserver is reachable including https:// 
- under Developers => API credentials click ws@Company.[your-adyen-account]
  - under "Server settings => Authentication" click "Generate API key", copy it and save it
  - under "Client settings => Authentication" click "Generate client key", copy it and save it
  - add allowed domains: the domain under your shop webserver is reachable including https://
