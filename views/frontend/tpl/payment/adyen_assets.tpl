<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.js"
        integrity="[{$oViewConf->getAdyenIntegrityJS()}]"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.css"
      integrity="[{$oViewConf->getAdyenIntegrityCSS()}]"
      crossorigin="anonymous">
[{assign var="sToken" value=$oViewConf->getSessionChallengeToken()}]
[{assign var="sSelfLink" value=$oViewConf->getSslSelfLink()|replace:"&amp;":"&"}]
[{if $phpStorm}]<script>[{/if}]
[{capture assign="adyenJS"}]
    [{if $oViewConf->getTopActiveClassName() == 'payment'}]
        const adyenStateEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamStateName()}]');

        const nextStepEl = document.getElementById('paymentNextStepBottom');
        [{* reset the disabled-status of paymentNextStepBottom if payment is changed *}]
        document.getElementsByName('paymentid').forEach(function (e) {
            e.addEventListener('change', function (event) {
                nextStepEl.disabled = false;
            });
        });
    [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
        [{assign var="orderPaymentPayPal" value=false}]
        [{assign var="paymentID" value=$payment->getId()}]
        [{if $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_PAYPAL_ID')}]
            [{assign var="orderPaymentPayPal" value=true}]
        [{/if}]
    [{/if}]
    const adyenAsync = async function () {
        const configuration = {
            environment: '[{$oViewConf->getAdyenOperationMode()}]',
            clientKey: '[{$oViewConf->getAdyenClientKey()}]',
            analytics: {
                [{* Set to false to not send analytics data to Adyen. *}]
                enabled: [{if $oViewConf->isAdyenLoggingActive()}]true[{else}]false[{/if}]
            },
            locale: '[{$oViewConf->getAdyenShopperLocale()}]',
            [{if $oViewConf->getTopActiveClassName() == 'payment'}]
                paymentMethodsResponse: [{$oViewConf->getAdyenPaymentMethods()}],
            [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
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
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.error(error.name, error.message, error.stack, component);
                [{/if}]
            },
            onChange: (state, component) => {
                [{if $oViewConf->getTopActiveClassName() == 'payment'}]
                    var paymentIdEl = document.getElementById(component._node.attributes.getNamedItem('data-paymentid').value);
                    paymentIdEl.checked = true;
                    // negate isValid to Button
                    nextStepEl.disabled = !state.isValid;
                    if (state.isValid) {
                        adyenStateEl.value = JSON.stringify(state.data.paymentMethod);
                    }
                [{/if}]
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.log('onChange:', state);
                [{/if}]
            },
            onSubmit: (state, component) => {
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.log('onSubmit:', state);
                [{/if}]
                component.setStatus('loading');
                makePayment(state.data)
                .then(response => {
                    if (response.action) {
                        // Drop-in handles the action object from the /payments response
                        component.handleAction(response.action);
                    } else {
                        // Your function to show the final result to the shopper
                        //showFinalResult(response);
                        console.log('toDo: Your function to show the final result to the shopper');
                    }
                })
                .catch(error => {
                    throw Error(error);
                });
            },
            onAdditionalDetails: (state, component) => {
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.log('onChange:', state);
                    console.log('onChange:', component);
                [{/if}]
            },
            paymentMethodsConfiguration: {
                [{if $oViewConf->getTopActiveClassName() == 'payment'}]
                    [{foreach key=paymentID from=$oView->getPaymentList() item=paymentObj name=paymentListJS}]
                        [{if $paymentObj->showInPaymentCtrl() && $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_CREDITCARD_ID')}]
                            card: {
                                hasHolderName: true,
                                holderNameRequired: true,
                                hideCVC: false
                            },
                        [{/if}]
                    [{/foreach}]
                [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
                    [{if $orderPaymentPayPal}]
                        paypal: {
                            intent: "authorize",
                            cspNonce: "MY_CSP_NONCE",
                            onShippingChange: function(data, actions) {
                                // Listen to shipping changes.
                                [{if $oViewConf->isAdyenLoggingActive()}]
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
        [{if $oViewConf->isAdyenLoggingActive()}]
            console.log(checkout.paymentMethodsResponse);
        [{/if}]
        [{if $oViewConf->getTopActiveClassName() == 'payment'}]
            [{foreach key=paymentID from=$oView->getPaymentList() item=paymentObj}]
                [{if $paymentObj->showInPaymentCtrl() && $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_CREDITCARD_ID')}]
                    const cardComponent = checkout.create('card').mount('#[{$paymentID}]-container');
                [{/if}]
            [{/foreach}]
        [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
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
        const httpPost = (endpoint, data) =>
            fetch('[{$sSelfLink}]cl=adyenjscontroller&fnc=' + endpoint + '&context=continue&stoken=[{$sToken}]', {
                method: 'POST',
                headers: {
                    Accept: 'application/json, text/plain, */*',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(response => response.json());
    }
    // Call adyenAsync
    adyenAsync();
[{/capture}]
[{if $phpStorm}]</script>[{/if}]
[{oxscript add=$adyenJS}]