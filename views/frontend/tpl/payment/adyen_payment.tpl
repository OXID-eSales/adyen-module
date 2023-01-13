<div class="well well-sm">
    <input
            style="display:none;"
            id="payment_[{$sPaymentID}]"
            type="radio"
            name="paymentid"
            value="[{$sPaymentID}]"
            data-showpaymentctrl="[{$paymentmethod->showInPaymentCtrl()}]"
            [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]
    />
    <div id="[{$sPaymentID}]-container" data-paymentid="payment_[{$sPaymentID}]"></div>
</div>
