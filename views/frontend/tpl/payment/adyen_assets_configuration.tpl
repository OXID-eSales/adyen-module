[{if $phpStorm}]<script>[{/if}]
const isLog = [{if $isLog}]true[{else}]false[{/if}];
const isPaymentPage = [{if $isPaymentPage}]true[{else}]false[{/if}];
const isOrderPage = [{if $isOrderPage}]true[{else}]false[{/if}];
const isCreditCard = [{if $orderPaymentCreditCard}]true[{else}]false[{/if}];
const isRedirectPayment = [{if $orderPaymentIsRedirect}]true[{else}]false[{/if}];

// Read agreement state by inspecting the visible checkboxes directly, the
// same way the PayPal module does it. The hidden inputs in the order form
// are only synced via a jQuery click handler in inc/agb.tpl, which races
// our own change listener; reading .checked sidesteps that. Returns null
// when the order form is missing (e.g. payment page) - the caller treats
// that as "no check needed".
const oscAdyenReadAgreements = function () {
    if (!document.getElementById('orderConfirmAgbBottom')) {
        return null;
    }
    const isChecked = function (id) {
        const el = document.getElementById(id);
        // missing checkbox => not required for this order
        return el ? !!el.checked : true;
    };
    const exists = function (id) {
        return !!document.getElementById(id);
    };
    return {
        ordAgb: isChecked('checkAgbTop'),
        downloadable: isChecked('oxdownloadableproductsagreement'),
        service: isChecked('oxserviceproductsagreement'),
        hasOrdAgb: exists('checkAgbTop'),
        hasDownloadable: exists('oxdownloadableproductsagreement'),
        hasService: exists('oxserviceproductsagreement')
    };
};

// Highlight the agbTop checkbox and scroll it into view so the shopper sees
// what is missing. Falls back gracefully when the element is absent.
const oscAdyenHighlightAgreements = function () {
    const target = document.querySelector('.agb.panel');
    if (!target) {
        return;
    }
    target.classList.add('osc-adyen-agb-missing');
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(function () {
        target.classList.remove('osc-adyen-agb-missing');
    }, 2500);
};
const configuration = {
    [{$configFields}],
    onError: (error, component) => {
        if (isLog) {
            console.error(error.name, error.message, error.stack, component);
        }
    },
    onChange: (state, component) => {
        if (isPaymentPage) {
            if (state.isValid) {
                const paymentIdEl = document.getElementById(component._node.attributes.getNamedItem('data-paymentid').value);
                nextStepEl.dataset.adyensubmit = paymentIdEl.value;
                nextStepEl.disabled = false;
            }
            else {
                nextStepEl.dataset.adyensubmit = '';
            }
        }
        if (isLog) {
            console.log('onChange:', state, component);
        }
    },
    onSubmit: (state, component) => {
        if (isLog) {
            console.log('onSubmit:', state.data);
        }
        if (isOrderPage && isRedirectPayment) {
            // Redirect-based methods (Twint, Klarna) take the browser away
            // before the order form ever submits, so OXID's regular AGB check
            // is bypassed. Enforce it client-side here. Without this guard
            // the shopper returns from the third-party page and OXID rejects
            // the order with "please confirm AGB" even when accepted earlier.
            const agreements = oscAdyenReadAgreements();
            if (agreements !== null) {
                const ordAgbMissing = agreements.hasOrdAgb && !agreements.ordAgb;
                const downloadableMissing = agreements.hasDownloadable && !agreements.downloadable;
                const serviceMissing = agreements.hasService && !agreements.service;
                if (ordAgbMissing || downloadableMissing || serviceMissing) {
                    if (isLog) {
                        console.log('AGB not confirmed, aborting redirect payment');
                    }
                    oscAdyenHighlightAgreements();
                    component.setStatus('ready');
                    return;
                }
            }
        }
        component.setStatus('loading');
        if (isPaymentPage || isCreditCard) {
            state.data.deliveryAddress = configuration.deliveryAddress;
            state.data.shopperEmail = configuration.shopperEmail;
            state.data.shopperIP = configuration.shopperIP;
        }
        makePayment(state.data) //this function is declared in adyen_assets.tpl
            .then(response => {
                if (isLog) {
                    console.log('onSubmit-response:', response);
                }
                if (response.action) {
                    component.handleAction(response.action);
                } else {
                    setPspReference(response);
                }
            })
            .catch(error => {
                throw Error(error);
            });
    },
    onAdditionalDetails: (state, component) => {
        makeDetailsCall(state.data)
            .then(response => {
                if (isPaymentPage) {
                    nextStepEl.dataset.adyensubmit = '';
                    nextStepEl.disabled = true;
                }
                if (isLog) {
                    console.log('makeDetailsCall:', response);
                }
                let resultSetPspReference = setPspReference(response);
                if (isPaymentPage) {
                    if (resultSetPspReference === false) {
                        nextStepEl.disabled = false;
                    }
                }
            })
            .catch(error => {
                throw Error(error);
            });
        if (isLog) {
            console.log('onAdditionalDetails:', state, component);
        }
    },
    paymentMethodsConfiguration: {
        [{if $paymentConfigNeedsCard}]
            card: {
                hasHolderName: true,
                holderNameRequired: true,
                hideCVC: false
            },
        [{elseif $isOrderPage && $orderPaymentPayPal}]
            paypal: {
                intent: "authorize",
                merchantId: "[{$payPalMerchantId}]",
                onShippingChange: function (data, actions) {
                    // Listen to shipping changes.
                    if (isLog) {
                        console.log('onPayPalShippingChange:', data);
                    }
                },
                onClick: () => {
                    // onClick is called when the button is clicked.
                },
                blockPayPalCreditButton: true,
                blockPayPalPayLaterButton: true
            }
        [{elseif $isOrderPage && $orderPaymentGooglePay}]
            googlepay: [{$googlePayConfigurationJson}],
        [{elseif $isOrderPage && $orderPaymentApplePay}]
            applepay: [{$applePayConfigurationJson}],
        [{/if}]
    }
};
