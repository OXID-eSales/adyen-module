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
    <!-- Show AdyenHistory -->
    <table style="width: 98%; border-spacing: 0;">
        <tr>
            <td class="listheader first">[{oxmultilang ident="OSC_ADYEN_PSPREFERENCE"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="OSC_ADYEN_PARENTPSPREFERENCE"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="GENERAL_PRICE"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="OSC_ADYEN_TIMESTAMP"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="OSC_ADYEN_STATUS"}]</td>
        </tr>
        [{assign var="blWhite" value=""}]
        [{foreach from=$history item=listitem name=historyList}]
            <tr id="art.[{$smarty.foreach.historyList.iteration}]">
                [{assign var="listclass" value=listitem$blWhite}]
                <td class="[{$listclass}]">[{$listitem->getPSPReference()}]</td>
                <td class="[{$listclass}]">[{$listitem->getParentPSPReference()}]</td>
                <td class="[{$listclass}]">[{$listitem->getFormatedPrice()}] [{$listitem->getCurrency()}]</td>
                <td class="[{$listclass}]">[{$listitem->getTimeStamp()}]</td>
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
        <div style="margin-top: 10px">
            <p><b>[{oxmultilang ident="OSC_ADYEN_COLLECTMONEY" suffix="COLON"}]</b></p>
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
    [{/if}]
[{/if}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
