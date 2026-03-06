# Security Considerations

This document describes known security considerations that have been reviewed and intentionally left unfixed, either because they require architectural changes, have very low practical risk, or are mitigated by other factors.

## Session-Amount Manipulation

**Files:** `src/Controller/PaymentController.php`, `src/Service/SessionSettings.php`
**Risk:** High

The Adyen payment amount (`amountValue`) and other payment data (`pspReference`, `resultCode`) are received from the client-side HTTP request and stored in the session. The validation in `isValidAdyenAuthorisation()` compares the basket amount against this client-supplied value.

A proper fix requires verifying the authorized amount server-side via the Adyen API (e.g., `GET /payments/{pspReference}`) before accepting the order. This is an architectural change that affects the entire payment flow.

**Mitigation:** The Adyen capture step (which actually charges the customer) uses the correct basket amount, not the session value. The risk is that an order could be marked as "authorized" in the shop with a mismatched amount, but the actual charge would use the correct amount.

**Recommendation:** Implement server-side amount verification via Adyen API in a future release.

## JSON Configuration Output in JavaScript Context

**Files:** `views/frontend/tpl/payment/adyen_assets_configuration.tpl`, `views/frontend/tpl/payment/adyen_assets.tpl`
**Risk:** Medium

JSON-encoded configuration (containing user data like email, name, address) is output directly in a JavaScript context using Smarty template syntax (e.g., `[{$configFields}]`). OXID 6.5 uses Smarty templates without auto-escaping, so the raw output relies entirely on `json_encode()` for safety. While `json_encode()` is inherently XSS-safe, the module applies regex post-processing (removing outer braces, unquoting keys) that makes the pattern fragile.

A proper fix would use `<script type="application/json">` blocks parsed with `JSON.parse()`, but this requires refactoring the Adyen Drop-in component initialization.

**Mitigation:** `json_encode()` escapes `<`, `>`, `&`, `'`, `"` characters. The regex post-processing has been reviewed and does not introduce injection vectors for the current data shapes.

## Race Condition in Webhook Processing

**Context:** Concurrent webhooks (e.g., AUTHORISATION + CAPTURE arriving simultaneously)
**Risk:** Medium

There is no database locking or mutex during webhook processing. Parallel webhooks could read stale order state and write inconsistent updates.

**Mitigation:** Adyen typically sends webhooks sequentially for the same payment, with retries on failure. The practical risk is low for normal payment flows. A full fix would require `SELECT FOR UPDATE` and a unique index on `pspreference` + `eventcode`.

## Webhook Idempotency

**File:** `src/Core/Webhook/Handler/WebhookHandlerBase.php`
**Risk:** Low

`setHistoryEntry()` creates a new database entry for each webhook call without duplicate checking. Adyen retries produce redundant history entries.

**Mitigation:** Redundant entries do not affect order processing correctness — they only inflate the history table. A unique index on `pspreference` + `eventcode` would solve this.

## Session Payment Data Integrity

**Files:** `src/Service/SessionSettings.php`, `src/Controller/PaymentController.php`
**Risk:** Medium (related to Session-Amount Manipulation above)

Beyond the amount, `pspReference` and `resultCode` are also taken from client requests. A forged `pspReference` could be stored in the session and treated as a valid Adyen payment reference.

**Mitigation:** Same as Session-Amount Manipulation — server-side verification via Adyen API is the proper solution.

---

*Last updated: 2026-03-06*
