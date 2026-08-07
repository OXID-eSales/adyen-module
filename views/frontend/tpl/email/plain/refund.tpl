[{assign var="shop" value=$oEmailView->getShop()}]
[{assign var="oViewConf" value=$oEmailView->getViewConfig()}]
[{block name="adyen_email_plain_refund_intro"}]
[{if $isAdyenOwnerMail}]
[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_INTRO_OWNER"}]
[{else}]
[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_SALUTATION"}] [{$order->oxorder__oxbillfname->getRawValue()}] [{$order->oxorder__oxbilllname->getRawValue()}],

[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_INTRO"}]
[{/if}]
[{/block}]

[{block name="adyen_email_plain_refund_details"}]
[{oxmultilang ident="ORDER_NUMBER" suffix="COLON"}] [{$order->oxorder__oxordernr->value}]
[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_AMOUNT" suffix="COLON"}] [{oxprice price=$adyenRefundedAmount currency=$currency}]
[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_ORDER_TOTAL" suffix="COLON"}] [{oxprice price=$order->oxorder__oxtotalordersum->value currency=$currency}]
[{/block}]

[{block name="adyen_email_plain_refund_note"}]
[{if !$isAdyenOwnerMail}]
[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_NOTE"}]
[{/if}]
[{/block}]

[{$shop->oxshops__oxname->getRawValue()}]
[{$shop->oxshops__oxurl->value}]
