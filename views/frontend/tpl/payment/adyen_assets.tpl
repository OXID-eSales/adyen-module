[{*
/* toDo: Currently we use the resources directly from Adyen.
* Please leave the old resources commented out until we are sure that we will no longer need the resources.
*/
[{assign var="sFileMTimeJS" value=$oViewConf->getModulePath('osc_adyen','out/src/js/adyen.min.js')|filemtime}]
[{assign var="sFileMTimeCSS" value=$oViewConf->getModulePath('osc_adyen','out/src/js/adyen.min.js')|filemtime}]
[{oxstyle include=$oViewConf->getModuleUrl('osc_adyen', 'out/src/css/adyen.min.css')|cat:"?"|cat:$sFileMTimeCSS priority=10}]
[{oxscript include=$oViewConf->getModuleUrl('osc_adyen','out/src/js/adyen.min.js')|cat:"?"|cat:$sFileMTimeJS priority=10}]
*}]
<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/5.27.0/adyen.js"
        integrity="sha384-YGWSKjvKe65KQJXrOTMIv0OwvG+gpahBNej9I3iVl4eMXhdUZDUwnaQdsNV5OCWp"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/5.27.0/adyen.css"
      integrity="sha384-2MpA/pwUY9GwUN1/eXoQL3SDsNMBV47TIywN1r5tb8JB4Shi7y5dyRZ7AwDsCnP8"
      crossorigin="anonymous">
[{capture assign="adyenJS"}]
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
        },

        // Any payment method specific configuration. Find the configuration specific to each payment method:  https://docs.adyen.com/payment-methods
        // For example, this is 3D Secure configuration for cards:
        paymentMethodsConfiguration: {
            card: {
                hasHolderName: true,
                holderNameRequired: true,
                billingAddressRequired: true
            }
        }
    };
    // Create an instance of AdyenCheckout using the configuration object.
    const checkout = new AdyenCheckout(configuration);
    // Access the available payment methods for the session.
    console.log(checkout.paymentMethodsResponse); // => { paymentMethods: [...], storedPaymentMethods: [...] }
    // Create an instance of the Component and mount it to the container you created.
    const cardComponent = checkout.create('card').mount('#card-container');
[{/capture}]
[{oxscript add=$adyenJS}]