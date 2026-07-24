# Change Log for OxidSolutionCatalysts Adyen

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.1.12] - unreleased

### FIX
- [0007976](https://bugs.oxid-esales.com/view.php?id=7976): Prevent orders from being finalized/recorded with an authorized amount that is lower than the order total. When a shopper started a redirect payment (e.g. Klarna) and then changed the basket in a parallel tab/session before completing it, Adyen only authorized the original (smaller) amount while the shop finalized the order for the current (larger) total. The backend then showed the full order value as "Authorized", and with delayed/two-step capture (Klarna captures only when the goods ship) this surfaced as an over-capture attempt at capture time. Two complementary changes:
  - **Record the real authorized amount:** `Service/PaymentGateway::doFinishAdyenPayment()` now reads `paymentDetails.amount.value` from the Adyen `/payments/details` response on redirect return and converts it currency-aware (via `AdyenPayment::getOxidAmount()` / the currency's decimals, so JPY/KWD etc. are handled correctly) before writing the AUTHORIZE history entry; immediate capture also captures this authorized amount rather than the order total (`Model/Order::captureAdyenOrder()` still caps it to the remaining capturable sum). Flows where Adyen reports no amount (in-page PaymentCtrl) keep the previous behaviour (fall back to the order total).
  - **Safety net (abort under-authorized orders):** `Controller/OrderController::return()` now compares the Adyen-authorized amount against the current basket gross total *before* finalizing (new `OrderReturnService::isAuthorizedAmountSufficient()`, integer minor-unit comparison). If the authorization does not cover the current cart, the pending Adyen authorization is cancelled (session-based `PaymentCancel`, mirroring `Model/Order::removeAdyenPaymentFromSession()`) and the shopper is kept on the order page with a new message (`OSC_ADYEN_RETURN_REASON_AMOUNT_MISMATCH`, de/en) to pay again — no under-authorized order is created. Normal orders (authorization covers the cart) are unaffected.
  - The currency-decimals resolution used by both paths was centralized in `AdyenPayment::getCurrencyDecimalsByName()`. Added regression unit tests. (Backport of the OXID 7 fix.)
- Twint and Klarna: enforce AGB acceptance before the redirect-payment button can be triggered, so the shopper cannot leave the order page without confirming the terms
- Twint and Klarna: accept the unchanged delivery address on return from the third-party page (the delivery-address value passed in the return URL was md5-hashed and therefore never matched OXID core's raw-string comparison)
- Twint and Klarna: visually disable the payment button while required agreement checkboxes are unchecked

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
