[{assign var="adyenPspReferenceEl" value=$oViewConf->getAdyenHtmlParamPspReferenceName()}]
[{assign var="adyenResultCodeEl" value=$oViewConf->getAdyenHtmlParamResultCodeName()}]
[{assign var="adyenAmountCurrencyEl" value=$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]
<input id="[{$adyenPspReferenceEl}]" type="hidden" name="[{$adyenPspReferenceEl}]" value="" />
<input id="[{$adyenResultCodeEl}]" type="hidden" name="[{$adyenResultCodeEl}]" value="" />
<input id="[{$adyenAmountCurrencyEl}]" type="hidden" name="[{$adyenAmountCurrencyEl}]" value="" />