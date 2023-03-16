[{assign var="sPaymentID" value=$payment->getId()}]
[{assign var="containerId" value=$payment->getTemplatePayButtonContainerId()}]
<div class="pull-right submitButton nextStep">
    [{oxmultilang ident="OSC_ADYEN_BUY_NOW_PAY_WITH"}]
    <div id="[{$containerId}]"
         data-paymentid="payment_[{$sPaymentID}]"
    ></div>
</div>
[{include file="modules/osc/adyen/payment/adyen_payment_psp.tpl"}]