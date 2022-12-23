[{assign var="sPaymentID" value=$payment->getId()}]
<div class="pull-right submitButton nextStep">
    [{oxmultilang ident="OSC_ADYEN_BUY_NOW_PAY_WITH"}]
    <div id="[{$sPaymentID}]-container"
         data-paymentid="payment_[{$sPaymentID}]"
    ></div>
</div>
[{assign var="adyenPspReferenceIdEl" value=$oViewConf->getAdyenHtmlParamPspReferenceName()}]
<input id="[{$adyenPspReferenceIdEl}]" type="hidden" name="[{$adyenPspReferenceIdEl}]" value="" />