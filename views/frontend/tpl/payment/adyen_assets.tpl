[{*
/* toDo: Currently we use the resources directly from Adyen.
* Please leave the old resources commented out until we are sure that we will no longer need the resources.
*/
[{assign var="sFileMTimeJS" value=$oViewConf->getModulePath('osc_adyen','out/src/js/adyen.min.js')|filemtime}]
[{assign var="sFileMTimeCSS" value=$oViewConf->getModulePath('osc_adyen','out/src/js/adyen.min.js')|filemtime}]
[{oxstyle include=$oViewConf->getModuleUrl('osc_adyen', 'out/src/css/adyen.min.css')|cat:"?"|cat:$sFileMTimeCSS priority=10}]
[{oxscript include=$oViewConf->getModuleUrl('osc_adyen','out/src/js/adyen.min.js')|cat:"?"|cat:$sFileMTimeJS priority=10}]
*}]
<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.js"
        integrity="[{$oViewConf->getAdyenIntegrityJS()}]"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.css"
      integrity="[{$oViewConf->getAdyenIntegrityCSS()}]"
      crossorigin="anonymous">
[{capture assign="adyenJS"}]
    var nextStepEl = document.getElementById('paymentNextStepBottom');
    var adyenStateEl = document.getElementById('adyenStateDataPaymentMethod');
    [{* reset the disabled-status of paymentNextStepBottom if payment is changed *}]
    document.getElementsByName('paymentid').forEach(function (e) {
        e.addEventListener('change', function (event) {
            nextStepEl.disabled = false;
        });
    });
    const adyenAsync = async function () {
        const configuration = {
            environment: '[{$oViewConf->getAdyenOperationMode()}]',
            clientKey: '[{$oViewConf->getAdyenClientKey()}]',
            analytics: {
                [{* Set to false to not send analytics data to Adyen. *}]
                enabled: [{if $oViewConf->isAdyenLoggingActive()}]true[{else}]false[{/if}]
            },
            [{*
            // Session is needed if we follow the Web Components integration guide !after! v5.0.0.
            // https://docs.adyen.com/online-payments/web-components
            // This is interesting for the case when we have an onPageCheckout
            session: {
                id: '[{$oViewConf->getAdyenSessionId()}]',
                sessionData: '[{$oViewConf->getAdyenSessionData()}]'
            },
            onPaymentCompleted: (result, component) => {
                [{if $oViewConf->isAdyenLoggingActive()}]
                     console.info(result, component);
                [{/if}]
            },
            onSubmit: (state, component) => {
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.log('onSubmit', state);
                [{/if}]
                component.setStatus('loading');
                makePayment(state.data, { amount, countryCode })
                    .then(this.handleResponse)
                    .catch(this.handleError);
                return true;
            }
            *}]
            locale: '[{$oViewConf->getAdyenShopperLocale()}]',
            paymentMethodsResponse: [{$oViewConf->getAdyenPaymentMethods()}],
            onError: (error, component) => {
                [{if $oViewConf->isAdyenLoggingActive()}]
                    console.error(error.name, error.message, error.stack, component);
                [{/if}]
            },
            onChange: (state, component) => {
                var paymentIdEl = document.getElementById(component._node.attributes.getNamedItem('data-paymentid').value);
                paymentIdEl.checked = true;
                // negate isValid to Button
                nextStepEl.disabled = !state.isValid;
                if (state.isValid) {
                    adyenStateEl.value = JSON.stringify(state.data.paymentMethod);
                }
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
        [{foreach key=paymentID from=$oView->getPaymentList() item=paymentObj}]
            [{if $paymentObj->isActiveAdyenPayment()}]
                [{if $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_CREDITCARD_ID')}]
                    const cardConfiguration = {
                        hasHolderName: true,
                        holderNameRequired: true,
                        billingAddressRequired: true, // Set to true to show the billing address input fields.
                    };
                    // Create an instance of the Component and mount it to the container you created.
                    const cardComponent = checkout.create('card').mount('#[{$paymentID}]-container');
                [{elseif $paymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_PAYPAL_ID')}]
                    const paypalComponent = checkout.create('paypal').mount('#[{$paymentID}]-container');
                [{/if}]
            [{/if}]
        [{/foreach}]
    }
    // Call adyenAsync
    adyenAsync();
[{/capture}]
[{oxscript add=$adyenJS}]