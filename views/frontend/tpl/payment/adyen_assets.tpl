<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.js"
        integrity="[{$oViewConf->getAdyenIntegrityJS()}]"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.css"
      integrity="[{$oViewConf->getAdyenIntegrityCSS()}]"
      crossorigin="anonymous">
[{capture assign="adyenJS"}]
    [{if $oViewConf->getTopActiveClassName() == 'payment'}]
        var adyenStateEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamStateName()}]');
        var nextStepEl = document.getElementById('paymentNextStepBottom');
        [{* reset the disabled-status of paymentNextStepBottom if payment is changed *}]
        document.getElementsByName('paymentid').forEach(function (e) {
            e.addEventListener('change', function (event) {
                nextStepEl.disabled = false;
            });
        });
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
            paymentMethodsResponse: [{$oViewConf->getAdyenPaymentMethods()}],
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
            onAdditionalDetails: (state, component) => {
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.log('onChange:', state);
                    console.log('onChange:', component);
                [{/if}]
            }
        };
        // Create an instance of AdyenCheckout using the configuration object.
        const checkout = await AdyenCheckout(configuration);
        // Access the available payment methods for the session.
        [{if $oViewConf->isAdyenLoggingActive()}]
            console.log(checkout.paymentMethodsResponse); // => { paymentMethods: [...], storedPaymentMethods: [...] }
        [{/if}]
        [{if $oViewConf->getTopActiveClassName() == 'payment'}]
            [{foreach key=paymentID from=$oView->getPaymentList() item=paymentObj}]
                [{if $paymentObj->showInPaymentCtrl()}]
                    [{if $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_CREDITCARD_ID')}]
                        const cardConfiguration = {
                            hasHolderName: true,
                            holderNameRequired: true,
                            hideCVC: false
                        };
                        // Create an instance of the Component and mount it to the container you created.
                        const cardComponent = checkout.create('card').mount('#[{$paymentID}]-container');
                    [{/if}]
                [{/if}]
            [{/foreach}]
        [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
            [{assign var="paymentID" value=$payment->getId()}]
            [{if $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_PAYPAL_ID')}]
                const paypalConfiguration = {
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
                };
                const paypalComponent = checkout.create('paypal').mount('#[{$paymentID}]-container');
            [{/if}]
        [{/if}]
    }
    // Call adyenAsync
    adyenAsync();
[{/capture}]
[{oxscript add=$adyenJS}]