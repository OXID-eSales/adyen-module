[{if $phpStorm}]<script>[{/if}]
const isLog = [{if $isLog}]true[{else}]false[{/if}];
const isPaymentPage = [{if $isPaymentPage}]true[{else}]false[{/if}];
const isOrderPage = [{if $isOrderPage}]true[{else}]false[{/if}];
const configuration = {
    [{$configFields}],
    onError: (error, component) => {
        if (isLog) {
            console.error(error.name, error.message, error.stack, component);
        }
    },
    onChange: (state, component) => {
        if (isPaymentPage) {
            var paymentIdEl = setPaymentIdEl(component, !state.isValid);
            if (state.isValid) {
                nextStepEl.dataset.adyensubmit = paymentIdEl.value;
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
        component.setStatus('loading');
        if (isPaymentPage) {
            state.data.deliveryAddress = configuration.deliveryAddress;
            state.data.shopperEmail = configuration.shopperEmail;
            state.data.shopperIP = configuration.shopperIP;
        }
        makePayment(state.data)
            .then(response => {
                if (isLog) {
                    console.log('onSubmit-response:', response);
                }
                if (response.action) {
                    // Drop-in handles the action object from the /payments response
                    if ('paymentIdViewEl' in component) {
                        component.paymentIdViewEl.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
                    }
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
                    setPaymentIdEl(component, true);
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
        [{/if}]
    }
};
