<div class="well well-sm">
    <input id="payment_[{$sPaymentID}]" type="hidden" name="paymentid" value="[{$sPaymentID}]" />
    <div id="[{$sPaymentID}]-container"></div>
</div>
[{capture assign="adyenJS"}]
    [{if $sPaymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_CREDITCARD_ID')}]
        const cardConfiguration = {
            hasHolderName: true,
            holderNameRequired: true,
            billingAddressRequired: true, // Set to true to show the billing address input fields.
        };
        // Create an instance of the Component and mount it to the container you created.
        const cardComponent = checkout.create('card').mount('#[{$sPaymentID}]-container');
    [{elseif $sPaymentID == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_PAYPAL_ID')}]
        const paypalComponent = checkout.create('paypal').mount('#[{$sPaymentID}]-container');
    [{/if}]
[{/capture}]
[{oxscript add=$adyenJS}]