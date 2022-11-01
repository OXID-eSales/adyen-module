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
    const adyenAsync = async function () {
        const configuration = {
            environment: '[{$oViewConf->getAdyenOperationMode()}]',
            clientKey: '[{$oViewConf->getAdyenClientKey()}]',
            analytics: {
                enabled: true // Set to false to not send analytics data to Adyen.
            },
            session: {
                id: '[{$oViewConf->getAdyenSessionId()}]',
                sessionData: '[{$oViewConf->getAdyenSessionData()}]'
            },
            onPaymentCompleted: (result, component) => {
                console.info(result, component);
            },
            onError: (error, component) => {
                console.error(error.name, error.message, error.stack, component);
            }
        };
        // Create an instance of AdyenCheckout using the configuration object.
        const checkout = await AdyenCheckout(configuration);
        // Access the available payment methods for the session.
        // console.log(checkout.paymentMethodsResponse); // => { paymentMethods: [...], storedPaymentMethods: [...] }
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