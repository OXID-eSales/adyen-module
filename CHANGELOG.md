# Change Log for OxidSolutionCatalysts Adyen

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.2.0] - unreleased

### NEW

- [0007986](https://bugs.oxid-esales.com/view.php?id=7986): Confirmation mails for refunds and cancellations triggered in the backend. Two new module settings in the module configuration (group "Confirmation mails") decide who is notified, separately per event: `osc_adyen_RefundMailRecipient` and `osc_adyen_CancelMailRecipient`, each with `0` no mail (default), `1` customer, `2` shop owner, `3` both. Defaults are `0`, so updating the module does not start sending mail to existing customers unannounced. The refund mail is sent at the point where Adyen accepted the refund (`Model/Order::refundAdyenOrder()`, right after the history entry is written), which covers the refund button in the order view (`Controller/Admin/AdminOrderController::refundAdyenAmount()`) and removing an order position (`Service/Controller/Admin/OrderArticleControllerService::refundOrderIfNeeded()`); it names order number, refunded amount and order total. The cancellation mail is sent by the existing `Model/Order::cancelOrder()` override after the cancellation was processed — that override is the single place both backend cancel paths run through (storno button in the order list and the Adyen cancel button in the order view), and it is the only place that knows whether the cancellation refunded money: if it did, that mail states the refunded amount and the refund mail is suppressed, so the customer receives one mail instead of two. New `Core/Email` (chain extension, four templates under `views/frontend/tpl/email/{html,plain}/`) and `Core/RefundMailService`, which is the only place deciding whether and to whom a mail goes out; mail or logging failures are caught there, because the refund or cancellation has already happened and must not surface as an error page in the backend. Rendering switches the admin mode off and back on, because these mails are triggered from the backend but use frontend templates and frontend language files - without that, core idents such as `ORDER_NUMBER` render as "ERROR: Translation for ORDER_NUMBER not found!" in the customer's mail. Both classes are deliberately free of trigger logic so they can move to the central payment base module later; the same feature is being rolled out to PayPal (0007984), Amazon Pay (0007985), Stripe (0007987) and Unzer (0007989).
- Follow-up on the confirmation mails above, after the first customer feedback: the customer facing texts named the payment provider inside the sentence ("we have issued a refund for you via Adyen." and "Adyen credits the amount to the payment method you used at Adyen."). For a provider that covers several payment methods that is too specific to be correct — the amount goes back to the card, bank account or wallet the customer actually paid with, not to "Adyen" as the customer reads it. `OSC_ADYEN_REFUND_MAIL_INTRO` and `OSC_ADYEN_REFUND_MAIL_NOTE` are therefore provider neutral now and word for word identical across all payment modules that offer refunds (PayPal, Amazon Pay, Adyen, Stripe, Unzer), so a shop running more than one of them no longer sends differently worded refund mails: "we have issued a refund for you." / "The refund has been credited to the payment method you originally used. When the amount becomes available depends on your payment method and your bank." `OSC_ADYEN_REFUND_MAIL_NOTE` is rendered by the cancellation mail as well (whenever an amount was refunded along with the cancellation), so that mail follows the same wording without a second ident. The shop owner copy keeps the provider information, but takes it out of the sentence: `OSC_ADYEN_REFUND_MAIL_INTRO_OWNER` now reads "A refund has been issued for the following order (Adyen Payment Provider)." — the mail is read next to the order in the backend, where knowing which provider moved the money is the point. The shop owner subjects (`OSC_ADYEN_REFUND_MAIL_SUBJECT_OWNER`, `OSC_ADYEN_CANCEL_MAIL_SUBJECT_OWNER`) keep their "Adyen:" prefix on purpose: a merchant who runs several payment modules sorts and filters these mails by that prefix, and a trailing parenthesis would not survive a truncated subject line in the inbox list. No code and no template change was needed — the provider names only ever lived in the language files (`translations/de/osc_adyen_lang.php`, `translations/en/osc_adyen_lang.php`); the mail templates address the idents only. The same wording change was made in the OXID 7 module `osc/adyen-module`.
- `Model/Order::refundAdyenOrder()` now takes the triggering backend action as a second parameter (`ModuleSettings::REFUND_CONTEXT_*`, default `refund`) and returns `bool` instead of `void`: `true` once Adyen accepted the refund. The cancellation flow needs that signal to decide whether its mail may name a refunded amount. Existing callers that ignore the return value are unaffected.
- Follow-up on the confirmation mails above: `Core/RefundMailService` was never registered in `services.yaml`, while `Model/Order` fetches it from the container. Every backend cancellation and every successful backend refund therefore ended in a fatal `ServiceNotFoundException` ("You have requested a non-existent service OxidSolutionCatalysts\Adyen\Core\RefundMailService") right after Adyen had accepted the modification - the cancellation or refund itself was done and recorded in the Adyen history, but the backend showed an error page and no mail was sent. The service is registered now (public, no arguments). Found while testing capture/refund/cancel against Checkout API v71. The same fix was made in the OXID 7 module `osc/adyen-module`.

### CHANGED

- Co-badged cards / Interchange Fees Regulation (IFR): upgrade to Adyen Web Components 6.21.0 and Checkout API v71, the combination Adyen names as "compliant out of the box" (https://docs.adyen.com/online-payments/co-badged-cards-compliance). The EU regulation requires that a shopper paying with a card that carries two schemes (e.g. Cartes Bancaires + Visa) can choose the scheme; Mastercard announced fines for non-compliant merchants after 2026-08-31. With Web SDK 5.27.0 and Checkout API v70 the module was below Adyen's requirement. The 6.21 card component renders the brand selection itself as soon as the BIN lookup returns more than one brand the merchant account supports, and the chosen brand travels in `state.data.paymentMethod.brand` to `/payments` unchanged - no own selector UI and no server-side change for the brand were needed. Which brands are offered still depends on the merchant account: a co-badged card only shows the choice if `/paymentMethods` returns both schemes for that account.
  - **Checkout API v71:** `adyen/php-api-library` stays on 14.x, which hardcodes v70. The new `Service/AdyenClient` extends `Adyen\Client` and returns `Module::ADYEN_CHECKOUT_API_VERSION` (`v71`) from `getApiCheckoutVersion()`; every checkout resource of the library builds its endpoint through that method, so payments, details, payment methods, captures, refunds and cancels switch together. `Service/AdyenSDKLoader` now creates this client. v70 -> v71 is additive for all endpoints the module uses (compared against Adyen's OpenAPI specifications).
  - **Web SDK 6.21.0:** `Core/Module::ADYEN_SDK_VERSION` and the SRI hashes are updated. The SDK is loaded from `checkoutshopper-{test|live}.cdn.adyen.com` - the previous host only redirects there for 6.x. **Shops with a Content Security Policy must allow `checkoutshopper-live.cdn.adyen.com` (and `-test` for sandbox) for scripts, styles, frames and connections**; 6.x also loads its translations from that host.
  - Template changes for the breaking changes of Web SDK 6 (`views/frontend/tpl/payment/adyen_assets.tpl`, `adyen_assets_configuration.tpl`): components are created with `AdyenWeb.createComponent()` instead of the removed `checkout.create()`; `AdyenCheckout` only receives its own core properties, the module specific fields (`deliveryAddress`, `shopperEmail`, `lineItems`, the per payment method configuration, ...) are passed to the components directly, because Web SDK 6 supports `paymentMethodsConfiguration` for Drop-in only; `onSubmit`/`onAdditionalDetails` finish via `actions.resolve()`/`actions.reject()` - for a `/payments` response with an action the SDK now handles the action itself, for a final response the module still writes pspReference, result code and amount into the order form before resolving, because `actions.resolve()` does not pass these on; the AGB guard for Twint/Klarna leaves the promise open on purpose, so the shopper only has to tick the box instead of running into the failed payment handling. The card component on the order page gets `showPayButton: false` instead of hiding Adyen's pay button via DOM in `onLoad` - the old selector picked the first `button` in the container, which is not safe once the component renders its own brand selection. The removed PayPal callback `onShippingChange` (only logged) is dropped.
  - `countryCode` is mandatory for `AdyenCheckout` since Web SDK 6 and is now set on the payment page as well (`Service/JSAPIConfigurationService`), not only on the order page.
  - The same change was made in the OXID 7 module `osc/adyen-module`.

## [1.1.12] - unreleased

### FIX
- Rename the `composer.json` key `conflicts` to `conflict`. Composer silently ignores the plural form, so the constraint meant to keep the module out of OXID eShop `<6.12 | ^7.0` was never evaluated and the module could be installed into a shop line it does not support — the incompatibility only surfaced at runtime. The key is renamed, the constraint itself is unchanged. Same fix as in the Amazon Pay module (2.2.1). The same fix was made in the OXID 7 module `osc/adyen-module`.
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
