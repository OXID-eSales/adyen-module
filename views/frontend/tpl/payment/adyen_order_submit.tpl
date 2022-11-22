[{assign var="sPaymentID" value=$payment->getId()}]
<button type="submit" class="submitButton nextStep" style="display:none;">
    Dummy Hidden Button
</button>
<div id="[{$sPaymentID}]-container" data-paymentid="payment_[{$sPaymentID}]"></div>