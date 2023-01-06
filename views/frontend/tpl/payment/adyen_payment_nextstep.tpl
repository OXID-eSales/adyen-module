[{assign var="adyenStateEl" value=$oViewConf->getAdyenHtmlParamStateName()}]
<input id="[{$adyenStateEl}]" type="hidden" name="[{$adyenStateEl}]" value="" />
[{include file="modules/osc/adyen/payment/adyen_payment_psp.tpl"}]