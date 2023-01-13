[{assign var="adyenPspReferenceEl" value=$oViewConf->getAdyenHtmlParamPspReferenceName()}]
[{assign var="adyenResultCodeEl" value=$oViewConf->getAdyenHtmlParamResultCodeName()}]
[{assign var="adyenAmountCurrencyEl" value=$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]
[{assign var="adyenAmountValueEl" value=$oViewConf->getAdyenHtmlParamAmountValueName()}]
<input id="[{$adyenPspReferenceEl}]" type="hidden" name="[{$adyenPspReferenceEl}]" value="" />
<input id="[{$adyenResultCodeEl}]" type="hidden" name="[{$adyenResultCodeEl}]" value="" />
<input id="[{$adyenAmountCurrencyEl}]" type="hidden" name="[{$adyenAmountCurrencyEl}]" value="" />
<input id="[{$adyenAmountValueEl}]" type="hidden" name="[{$adyenAmountValueEl}]" value="" />