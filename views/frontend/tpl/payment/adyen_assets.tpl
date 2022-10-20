<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/5.28.1/adyen.js"
        integrity="sha384-SGA+BK9i1sG5N4BTCgRH6EGbopUK8WG/azn/TeIHYeBEXmEaB+NT+410Z9b1ii7Z"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/5.28.1/adyen.css"
      integrity="sha384-oT6lIQpTr+nOu+yFBPn8mSMkNQID9wuEoTw8lmg2bcrFoDu/Ae8DhJVj+T5cUmsM"
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
const checkout = AdyenCheckout(configuration);
// Access the available payment methods for the session.
console.log(checkout.paymentMethodsResponse); // => { paymentMethods: [...], storedPaymentMethods: [...] }
// Create an instance of the Component and mount it to the container you created.
const cardComponent = checkout.create('card').mount('#card-container');
[{/capture}]
[{oxscript add=$adyenJS}]