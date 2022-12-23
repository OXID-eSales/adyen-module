[{assign var="sPaymentID" value=$payment->getId()}]
<div class="pull-right submitButton nextStep">
    [{oxmultilang ident="OSC_ADYEN_BUY_NOW_PAY_WITH"}]
    <div id="[{$sPaymentID}]-container"
         data-paymentid="payment_[{$sPaymentID}]"
    ></div>
</div>
[{assign var="adyenPspReferenceEl" value=$oViewConf->getAdyenHtmlParamPspReferenceName()}]
[{assign var="adyenResultCodeEl" value=$oViewConf->getAdyenHtmlParamResultCodeName()}]
[{assign var="adyenAmountCurrencyEl" value=$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]
<input id="[{$adyenPspReferenceEl}]" type="hidden" name="[{$adyenPspReferenceEl}]" value="" />
<input id="[{$adyenResultCodeEl}]" type="hidden" name="[{$adyenResultCodeEl}]" value="" />
<input id="[{$adyenAmountCurrencyEl}]" type="hidden" name="[{$adyenAmountCurrencyEl}]" value="" />