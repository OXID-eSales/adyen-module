[{assign var="sPaymentID" value=$payment->getId()}]
[{assign var="templateId" value=$payment->getTemplateId()}]
<div class="pull-right submitButton nextStep">
    [{oxmultilang ident="OSC_ADYEN_BUY_NOW_PAY_WITH"}]
    <div id="[{$templateId}]-container"
         data-paymentid="payment_[{$sPaymentID}]"
    ></div>
</div>
[{include file="modules/osc/adyen/payment/adyen_payment_psp.tpl"}]