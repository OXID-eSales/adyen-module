[{assign var="adyenPspReferenceEl" value=$oViewConf->getAdyenHtmlParamPspReferenceName()}]
[{assign var="adyenResultCodeEl" value=$oViewConf->getAdyenHtmlParamResultCodeName()}]
[{assign var="adyenAmountCurrencyEl" value=$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]
[{assign var="adyenAdjustAuthorisationEl" value=$oViewConf->getAdyenHtmlParamAdjustAuthorisationName()}]
<input id="[{$adyenPspReferenceEl}]" type="hidden" name="[{$adyenPspReferenceEl}]" value="" />
<input id="[{$adyenResultCodeEl}]" type="hidden" name="[{$adyenResultCodeEl}]" value="" />
<input id="[{$adyenAmountCurrencyEl}]" type="hidden" name="[{$adyenAmountCurrencyEl}]" value="" />
<input id="[{$adyenAdjustAuthorisationEl}]" type="hidden" name="[{$adyenAdjustAuthorisationEl}]" value="" />