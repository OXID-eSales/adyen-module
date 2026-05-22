# Change Log for OxidSolutionCatalysts Adyen

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.1.10] - unreleased

### FIX
- Twint and Klarna: enforce AGB acceptance before the redirect-payment button can be triggered, so the shopper cannot leave the order page without confirming the terms
- Twint and Klarna: visually disable the payment button while required agreement checkboxes are unchecked
- Twint and Klarna: accept the agreement state on return from the third-party page (the order form is gone after the redirect, so the hidden AGB inputs need to be reinstated server-side)

## [2.1.9] - 2026-04-09

### Security

- Fix critical HMAC webhook bypass: change default `isHMACVerified` to `false` (fail-closed)
- Fix HMAC bypass via injected `hmacSignatureUtil` in webhook payload: always use `new HmacSignature()`
- Set `isHMACVerified = false` on any exception during HMAC validation (catch `\Throwable`)
- Remove commented-out `return true` debug bypass in `Event::isHMACVerified()`
- Add merchant account verification (`isMerchantVerified()`) to `AdyenWebhookController`
- Add CSRF protection (`checkSessionChallenge`) to `AdyenJSController::payments()` and `details()`
- Fix SQL operator precedence in `AdyenHistoryList::getOxidOrderIdByPSPReference()` using `expr()->orX()`
- Replace direct `$_GET` access with `Registry::getRequest()` in `OrderReturnService`
- Remove `|raw` from payment description output in checkout template
- Remove XDEBUG_SESSION_START parameter from Adyen fetch URL
- Add SECURITY.md documenting known security considerations and intentionally unfixed items

## [2.1.8] - 2025-09-19

- Fix that Captured is shown two times in order history and therefore refundable amount is wrong
- don't mark order as paid when authorizing payment
- fix address selection

## [2.1.7] - 2025-03-28

- move CreditCard-Form to the last page in checkout

## [2.1.6] - 2025-02-07

- request Adyen cancellation when finalizeOrder fails due to stock exceptions

## [2.1.5] - 2024-03-05

- [0007571](https://bugs.oxid-esales.com/view.php?id=7571): Fix It is possible to complete a purchase (with the Adyen payment method) without paying
- show ApplePay only if it is possible

## [2.1.4] - 2024-02-23

- [0007569](https://bugs.oxid-esales.com/view.php?id=7569): Fix Maintenance Mode after entering Sandbox Data
- Take over all changes from Version 1.1.2 to 1.1.4
- Split Version for OXID 7

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
