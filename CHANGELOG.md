# Change Log for OxidSolutionCatalysts Adyen

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.1.11] - 2026-04-09

### Security

- Fix critical HMAC webhook bypass: change default `isHMACVerified` to `false` (fail-closed)
- Fix HMAC bypass via injected `hmacSignatureUtil` in webhook payload: always use `new HmacSignature()`
- Set `isHMACVerified = false` on any exception during HMAC validation (catch `\Throwable`)
- Remove commented-out `return true` debug bypass in `Event::isHMACVerified()`
- Add merchant account verification (`isMerchantVerified()`) to `AdyenWebhookController`
- Add CSRF protection (`checkSessionChallenge`) to `AdyenJSController::payments()` and `details()`
- Fix SQL operator precedence in `AdyenHistoryList::getOxidOrderIdByPSPReference()` using `expr()->orX()`
- Replace direct `$_GET` access with `Registry::getRequest()` in `OrderReturnService`
- Remove XDEBUG_SESSION_START parameter from Adyen fetch URL
- Add SECURITY.md documenting known security considerations and intentionally unfixed items

## [1.1.10] - 2025-09-19

## FIX
- don't mark order as paid when authorizing payment
- fix address selection

## [1.1.9] - 2025-02-07

## FIX
- Fix github workflows docker compose
- request Adyen cancellation when finalizeOrder fails due to stock exceptions

## [1.1.8] - 2024-11-19

### FIX
- use Adyen assets on the payment page again so that ApplePay is hidden if necessary. Was accidentally removed in release v1.1.7

## [1.1.7] - 2024-10-18

### NEW
- Creditcard-Fields move from Payment-Controller to the Order-Controller.

### FIX
- Fix URLs with real slashes instead of DIRECTORY_SEPERATOR

## [1.1.6] - 2024-06-21

- add new Webhookhandler "refund failed" & "report available"
- Fix that Captured is shown two times in order history and therefore refundable amount is wrong

## [1.1.5] - 2024-03-05

- [0007571](https://bugs.oxid-esales.com/view.php?id=7571): Fix It is possible to complete a purchase (with the Adyen payment method) without paying

## [1.1.4] - 2024-02-23

- [0007569](https://bugs.oxid-esales.com/view.php?id=7569): Fix Maintenance Mode after entering Sandbox Data
- Dont show ApplePay if ApplePay not possible

## [1.1.3] - 2023-12-01

# Fixed
- Compatibility-Issue with Unzer-Module
- Maintanence-Mode in Module-Config-Section

## [1.1.2] - 2023-11-14

# Fixed
- fix Update-Issues in case of missing service-registration

## [1.1.1] - 2023-09-08

# Fixed
- fix Core-Compatibilities-Issues with return-types

## [1.1.0] - 2023-08-28

# Fixed
- prevent wrong delivery address shown up in PayPal modal

## [1.0.1] - 2023-05-19

# Fixed
- prevent wrong submit payment (without paymentinformations) by clicking 'orderStep'-Link

# Changed
- actual Payments: CreditCard, PayPal, Google Pay, Klarna Sofortbezahlung, Klarna, klarna Ratenzahlung, Twint, Apple Pay

## [1.0.0] - 2023-02-01

# Changed
- initial release
- actual Payments: CreditCard, PayPal
