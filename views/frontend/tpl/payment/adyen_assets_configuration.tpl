[{if $phpStorm}]<script>[{/if}]
const isLog = [{if $isLog}]true[{else}]false[{/if}];
const isPaymentPage = [{if $isPaymentPage}]true[{else}]false[{/if}];
const isOrderPage = [{if $isOrderPage}]true[{else}]false[{/if}];
const isCreditCard = [{if $orderPaymentCreditCard}]true[{else}]false[{/if}];
const configuration = {
    [{$configFields}],
    onError: (error, component) => {
        if (isLog) {
            console.error(error.name, error.message, error.stack, component);
        }
    },
    onChange: (state, component) => {
        if (isPaymentPage) {
            //TODO: find out what this does and whether it's necessary for acdc to work in last step
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
