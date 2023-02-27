<script src="https://pay.google.com/gp/p/js/pay.js"></script>

<script>
    [{* we are using google pay api 2.0*}]
    const baseRequest = {
        apiVersion: 2,
        apiVersionMinor: 0
    };

    const tokenizationSpecification = {
        type: 'PAYMENT_GATEWAY',
        parameters: {
            'gateway': 'adyen',
            'gatewayMerchantId': '[{$oViewConf->getAdyenMerchantAccount()}]'
        }
    };

    function () {

    }
</script>