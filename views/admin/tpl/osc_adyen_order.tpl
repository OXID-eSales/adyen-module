[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="order_main">
</form>

[{if !$oView->isAdyenOrder()}]
    <div class="errorbox">
        [{oxmultilang ident="OSC_ADYEN_NO_ADYEN_ORDER"}]
    </div>
[{else}]
    [{assign var="adyenCurrency" value=$edit->oxorder__oxcurrency->value}]
    [{* toDo: The Capture Amount can be smaller if captures have already taken place. Then it has to be recalculated accordingly. *}]
    [{assign var="adyenCaptureAmount" value=$edit->getTotalOrderSum()}]
    [{* toDo: The Refund Amount can be smaller if refunds have already taken place. Then it has to be recalculated accordingly. *}]
    [{assign var="adyenRefundAmount" value=$edit->getTotalOrderSum()}]
    <table style="width: 98%; border-spacing: 0;">
        <tr>
            <!-- left side -->
            <td style="width:68%; padding:1%; vertical-align: text-top;">
                <!-- Show AdyenHistory -->
                <h3 style="margin-bottom: 20px;">[{oxmultilang ident="OSC_ADYEN_HISTORY"}]</h3>
                <table style="width: 98%; border-spacing: 0;">
                    <tr>
                        <td class="listheader first">[{oxmultilang ident="OSC_ADYEN_PSPREFERENCE"}]</td>
                        <td class="listheader">[{oxmultilang ident="OSC_ADYEN_PARENTPSPREFERENCE"}]</td>
                        <td class="listheader">[{oxmultilang ident="GENERAL_PRICE"}]</td>
                        <td class="listheader">[{oxmultilang ident="OSC_ADYEN_TIMESTAMP"}]</td>
                        <td class="listheader">[{oxmultilang ident="OSC_ADYEN_ACTION"}]</td>
                        <td class="listheader">[{oxmultilang ident="OSC_ADYEN_STATUS"}]</td>
                    </tr>
                    [{assign var="blWhite" value=""}]
                    [{foreach from=$history item=listitem name=historyList}]
                        [{assign var="actionIdent" value="OSC_ADYEN_ACTION"|cat:$listitem->getAdyenAction()}]
                        <tr id="art.[{$smarty.foreach.historyList.iteration}]">
                            [{assign var="listclass" value=listitem$blWhite}]
                            <td class="[{$listclass}]">[{$listitem->getPSPReference()}]</td>
                            <td class="[{$listclass}]">[{$listitem->getParentPSPReference()}]</td>
                            <td class="[{$listclass}]">[{$listitem->getFormatedPrice()}] [{$listitem->getCurrency()}]</td>
                            <td class="[{$listclass}]">[{$listitem->getTimeStamp()}]</td>
                            <td class="[{$listclass}]">[{oxmultilang ident=$actionIdent}]</td>
                            <td class="[{$listclass}]">[{$listitem->getAdyenStatus()}]</td>
                        </tr>
                        [{if $blWhite == "2"}]
                            [{assign var="blWhite" value=""}]
                        [{else}]
                            [{assign var="blWhite" value="2"}]
                        [{/if}]
                    [{/foreach}]
                </table>
                <!-- Show AdyenHistory END -->
                [{if $oView->isAdyenCapturePossible()}]
                    <!-- Adyen Capture -->
                    <div style="margin-top: 20px;">
                        <h3 style="margin-bottom: 20px;">[{oxmultilang ident="OSC_ADYEN_CAPTUREMONEY"}]</h3>
                        <form action="[{$oViewConf->getSelfLink()}]" method="post">
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="fnc" value="captureAdyenAmount" />
                            <input type="hidden" name="oxid" value="[{$oxid}]" />
                            <input type="hidden" name="cl" value="[{$oViewConf->getTopActiveClassName()}]" />
                            <input type="text"
                                   name="capture_amount"
                                   value="[{$adyenCaptureAmount|escape|string_format:"%.2f"}]" />
                            <input type="hidden"
                                   name="capture_currency"
                                   value="[{$adyenCurrency}]" />

                            <input type="submit" value="[{oxmultilang ident="OSC_ADYEN_CAPTURE"}]" />
                        </form>
                    </div>
                    <!-- Adyen Capture END -->
                [{/if}]
                [{if $oView->isAdyenRefundPossible()}]
                <!-- Adyen Refund -->
                <div style="margin-top: 20px;">
                    <h3 style="margin-bottom: 20px;">[{oxmultilang ident="OSC_ADYEN_REFUNDMONEY"}]</h3>
                    <form action="[{$oViewConf->getSelfLink()}]" method="post">
                        [{$oViewConf->getHiddenSid()}]
                        <input type="hidden" name="fnc" value="refundAdyenAmount" />
                        <input type="hidden" name="oxid" value="[{$oxid}]" />
                        <input type="hidden" name="cl" value="[{$oViewConf->getTopActiveClassName()}]" />
                        <input type="text"
                               name="refund_amount"
                               value="[{$adyenRefundAmount|escape|string_format:"%.2f"}]" />
                        <input type="hidden"
                               name="refund_currency"
                               value="[{$adyenCurrency}]" />

                        <input type="submit" value="[{oxmultilang ident="OSC_ADYEN_REFUND"}]" />
                    </form>
                </div>
                <!-- Adyen Refund END -->
                [{/if}]
            </td>
            <!-- left side END, right side -->
            <td style="width:28%; padding:1%; vertical-align: text-top;">
            </td>
            <!-- right side END -->
        </tr>
    </table>

[{/if}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
