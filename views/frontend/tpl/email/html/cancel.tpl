[{assign var="shop" value=$oEmailView->getShop()}]
[{assign var="oViewConf" value=$oEmailView->getViewConfig()}]

[{capture assign="style"}]
    table.adyencancel th, table.adyencancel td {
        border: 1px solid #d4d4d4;
        font-size: 13px;
        padding: 5px;
        white-space: nowrap;
    }

    table.adyencancel {
        border-collapse: collapse;
    }
[{/capture}]

[{include file="email/html/header.tpl" title="OSC_ADYEN_CANCEL_MAIL_TITLE"|oxmultilangassign|cat:" #"|cat:$order->oxorder__oxordernr->value style=$style}]

    [{block name="adyen_email_html_cancel_intro"}]
        <p>
            [{if $isAdyenOwnerMail}]
                [{oxmultilang ident="OSC_ADYEN_CANCEL_MAIL_INTRO_OWNER"}]
            [{else}]
                [{oxmultilang ident="OSC_ADYEN_CANCEL_MAIL_SALUTATION"}]
                [{$order->oxorder__oxbillfname->getRawValue()}] [{$order->oxorder__oxbilllname->getRawValue()}],
            [{/if}]
        </p>
        [{if !$isAdyenOwnerMail}]
            <p>[{oxmultilang ident="OSC_ADYEN_CANCEL_MAIL_INTRO"}]</p>
        [{/if}]
    [{/block}]

    [{block name="adyen_email_html_cancel_details"}]
        <table class="adyencancel" border="0" cellspacing="0" cellpadding="0" width="100%">
            <tbody>
                <tr valign="top">
                    <th align="right" class="text-right">[{oxmultilang ident="ORDER_NUMBER" suffix="COLON"}]</th>
                    <td>[{$order->oxorder__oxordernr->value}]</td>
                </tr>
                <tr valign="top">
                    <th align="right" class="text-right">[{oxmultilang ident="OSC_ADYEN_CANCEL_MAIL_ORDER_TOTAL" suffix="COLON"}]</th>
                    <td>[{oxprice price=$order->oxorder__oxtotalordersum->value currency=$currency}]</td>
                </tr>
                [{if $adyenRefundedAmount !== null}]
                    <tr valign="top">
                        <th align="right" class="text-right">[{oxmultilang ident="OSC_ADYEN_CANCEL_MAIL_REFUNDED" suffix="COLON"}]</th>
                        <td>[{$adyenRefundedAmount|string_format:"%.2f"}] [{$adyenCurrencyCode}]</td>
                    </tr>
                [{/if}]
            </tbody>
        </table>
        <br/>
    [{/block}]

    [{block name="adyen_email_html_cancel_note"}]
        [{if !$isAdyenOwnerMail}]
            [{if $adyenRefundedAmount !== null}]
                <p>[{oxmultilang ident="OSC_ADYEN_REFUND_MAIL_NOTE"}]</p>
            [{else}]
                <p>[{oxmultilang ident="OSC_ADYEN_CANCEL_MAIL_NOTE_NO_REFUND"}]</p>
            [{/if}]
        [{/if}]
    [{/block}]

[{include file="email/html/footer.tpl"}]
