[{assign var="sPaymentID" value=$payment->getId()}]
<button id="customPayButton" type="submit" class="submitButton nextStep">
    Jetzt kaufen
</button>
<div id="[{$sPaymentID}]-container" data-paymentid="payment_[{$sPaymentID}]"></div>