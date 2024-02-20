<div class="well well-sm">
    <input
            style="[{if $sPaymentID != 'oscadyencreditcard'}]display:none;[{/if}]"
            id="payment_[{$sPaymentID}]"
            type="radio"
            name="paymentid"
            value="[{$sPaymentID}]"
            data-showpaymentctrl="[{$paymentmethod->showInPaymentCtrl()}]"
            [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]
    />
    [{if $sPaymentID == 'oscadyencreditcard'}]
        <label for="payment_oscadyencreditcard"><b>[{oxmultilang ident="OSC_ADYEN_CHECKOUT_CC"}]</b></label>
    [{/if}]
    <div id="[{$sPaymentID}]-container" data-paymentid="payment_[{$sPaymentID}]"></div>
</div>
