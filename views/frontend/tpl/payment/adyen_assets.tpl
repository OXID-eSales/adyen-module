<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.js"
        integrity="[{$oViewConf->getAdyenIntegrityJS()}]"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.css"
      integrity="[{$oViewConf->getAdyenIntegrityCSS()}]"
      crossorigin="anonymous">
[{assign var="sToken" value=$oViewConf->getSessionChallengeToken()}]
[{assign var="sSelfLink" value=$oViewConf->getSslSelfLink()|replace:"&amp;":"&"}]
[{assign var="adyenCreditCard" value=$oViewConf->getAdyenPaymentCreditCardId()}]
[{assign var="adyenPayPal" value=$oViewConf->getAdyenPaymentPayPalId()}]
[{if $phpStorm}]<script>[{/if}]
    [{capture assign="adyenJS"}]
        [{assign var="isLog" value=$oViewConf->isAdyenLoggingActive()}]
        [{assign var="isPaymentPage" value=false}]
        [{assign var="isOrderPage" value=false}]
        let submitForm;
        [{if $oViewConf->getTopActiveClassName() == 'payment'}]
            [{assign var="isPaymentPage" value=true}]
            submitForm = document.getElementById('payment');
            const adyenStateEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamStateName()}]');
            const nextStepEl = document.getElementById('paymentNextStepBottom');
            [{* reset the disabled-status of paymentNextStepBottom if payment is changed *}]
            document.getElementsByName('paymentid').forEach(function (e) {
                e.addEventListener('change', function () {
                    nextStepEl.disabled = false;
                    nextStepEl.dataset.adyensubmit = '';
                });
            });
        [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
            [{assign var="isOrderPage" value=true}]
            [{assign var="orderPaymentPayPal" value=false}]
            [{assign var="paymentID" value=$payment->getId()}]
            [{if $paymentID === $adyenPayPal}]
                [{assign var="orderPaymentPayPal" value=true}]
            [{/if}]
            submitForm = document.getElementById('orderConfirmAgbBottom');
        [{/if}]
        const adyenPspReferenceEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamPspReferenceName()}]');
        const adyenResultCodeEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamResultCodeName()}]');
        const adyenAmountCurrencyEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]');

        const adyenAsync = async function () {
            const configuration = {
                environment: '[{$oViewConf->getAdyenOperationMode()}]',
                clientKey: '[{$oViewConf->getAdyenClientKey()}]',
                analytics: {
                    [{* Set to false to not send analytics data to Adyen. *}]
                    enabled: [{if $isLog}]true[{else}]false[{/if}]
                },
                locale: '[{$oViewConf->getAdyenShopperLocale()}]',
                deliveryAddress: [{$oView->getAdyenDeliveryAddress()}],
                shopperName: [{$oView->getAdyenShopperName()}],
                shopperEmail: '[{$oView->getAdyenShopperEmail()}]',
                shopperIP: '[{$oViewConf->getRemoteAddress()}]',
                [{if $isPaymentPage}]
                    paymentMethodsResponse: [{$oViewConf->getAdyenPaymentMethods()}],
                [{elseif $isOrderPage}]
                    countryCode: '[{$oViewConf->getAdyenCountryIso()}]',
                    amount: {
                        currency: '[{$oViewConf->getAdyenAmountCurrency()}]',
                        value: [{$oViewConf->getAdyenAmountValue()}]
                    },
                    [{if $orderPaymentPayPal}]
                        merchantId: '[{$oViewConf->getAdyenPayPalMerchantId()}]',
                    [{/if}]
                [{/if}]
                onError: (error, component) => {
                    [{if $isLog}]
                        console.error(error.name, error.message, error.stack, component);
                    [{/if}]
                },
                onChange: (state, component) => {
                    [{if $isPaymentPage}]
                        const paymentIdEl = document.getElementById(component._node.attributes.getNamedItem('data-paymentid').value);
                        paymentIdEl.checked = true;
                        // negate isValid to Button
                        nextStepEl.disabled = !state.isValid;
                        nextStepEl.dataset.adyensubmit = '';
                        if (state.isValid) {
                            nextStepEl.dataset.adyensubmit = paymentIdEl.value;
                            state.data.deliveryAddress = configuration.deliveryAddress;
                            state.data.shopperEmail = configuration.shopperEmail;
                            state.data.shopperIP = configuration.shopperIP;
                            adyenStateEl.value = JSON.stringify(state.data);
                        }
                    [{/if}]
                    [{if $isLog}]
                        console.log('onChange:', state, component);
                    [{/if}]
                },
                onSubmit: (state, component) => {
                    [{if $isLog}]
                        console.log('onSubmit:', state.data);
                    [{/if}]
                    component.setStatus('loading');
                    makePayment(state.data)
                        .then(response => {
                            [{if $isLog}]
                                console.log('onSubmit-response:', response);
                            [{/if}]
                            if (response.action) {
                                // Drop-in handles the action object from the /payments response
                                if (component.paymentIdViewEl !== 'undefined') {
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
                            [{if $isLog}]
                                console.log('makeDetailsCall:', response);
                            [{/if}]
                            setPspReference(response);
                        })
                        .catch(error => {
                            throw Error(error);
                        }),
                    [{if $isLog}]
                        console.log('onAdditionalDetails:', state, component);
                    [{/if}]
                },
                paymentMethodsConfiguration: {
                    [{if $isPaymentPage}]
                        [{foreach key=paymentID from=$oView->getPaymentList() item=paymentObj name=paymentListJS}]
                            [{if $paymentObj->showInPaymentCtrl() && $paymentID === $adyenCreditCard}]
                                card: {
                                    hasHolderName: true,
                                    holderNameRequired: true,
                                    hideCVC: false
                                },
                            [{/if}]
                        [{/foreach}]
                    [{elseif $isOrderPage}]
                        [{if $orderPaymentPayPal}]
                            paypal: {
                                intent: "authorize",
                                onShippingChange: function(data, actions) {
                                    // Listen to shipping changes.
                                    [{if $isLog}]
                                        console.log('onPayPalShippingChange:', data);
                                    [{/if}]
                                },
                                onClick: () => {
                                    // onClick is called when the button is clicked.
                                },
                                blockPayPalCreditButton: true,
                                blockPayPalPayLaterButton: true
                            }
                        [{/if}]
                    [{/if}]
                }
            };

            const checkout = await AdyenCheckout(configuration);
            // Access the available payment methods for the session.
            [{if $isLog}]
                console.log(checkout.paymentMethodsResponse);
            [{/if}]
            [{if $isPaymentPage}]
                [{foreach key=paymentID from=$oView->getPaymentList() item=paymentObj}]
                    [{if $paymentObj->showInPaymentCtrl() && $paymentID === $adyenCreditCard}]
                        const cardComponent = checkout.create('card').mount('#[{$paymentID}]-container');
                        cardComponent.paymentIdViewEl = undefined;
                    [{/if}]
                [{/foreach}]
            [{elseif $isOrderPage}]
                [{if $orderPaymentPayPal}]
                    const paypalComponent = checkout.create('paypal').mount('#[{$paymentID}]-container');
                [{/if}]
            [{/if}]

            const makePayment = (paymentMethod = {}) => {
                const paymentRequest = {paymentMethod};
                return httpPost('payments', paymentRequest)
                    .then(response => {
                        if (response.error) throw new Error('Payment initiation failed');
                        return response;
                    })
                    .catch(console.error);
            };

            const makeDetailsCall = data =>
                httpPost('details', data)
                    .then(response => {
                        if (response.error || response.errorCode) throw new Error('Details call failed');
                        return response;
                    })
                    .catch(err => console.error(err));

            const httpPost = (endpoint, data) =>
                fetch('[{$sSelfLink}]cl=adyenjscontroller&fnc=' + endpoint + '&context=continue&stoken=[{$sToken}]', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json, text/plain, */*',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                }).then(response => response.json());

            const setPspReference = (response) => {
                if (response.pspReference) {
                    adyenPspReferenceEl.value = response.pspReference;
                    adyenResultCodeEl.value = response.resultCode;
                    adyenAmountCurrencyEl.value = response.amount.currency;
                    if (typeof submitForm !== 'undefined') {
                        submitForm.submit();
                    }
                }
            }

            [{if $isPaymentPage}]
                nextStepEl.addEventListener("click", function(e) {
                    if (this.dataset.adyensubmit !== '') {
                        e.preventDefault();
                        this.disabled = true;
                        if (this.dataset.adyensubmit === '[{$adyenCreditCard}]') {
                            cardComponent.paymentIdViewEl = document.getElementById('payment_[{$adyenCreditCard}]').parentElement;
                            cardComponent.submit();
                        }
                    }
                }, false);
            [{/if}]
        }
        // Call adyenAsync
        adyenAsync();

    [{/capture}]
    [{if $phpStorm}]</script>[{/if}]
[{oxscript add=$adyenJS}]