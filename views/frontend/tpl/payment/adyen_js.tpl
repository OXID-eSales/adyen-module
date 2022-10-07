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
[{/capture}]
[{oxscript add=$adyenJS}]