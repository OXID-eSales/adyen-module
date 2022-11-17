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
[{/if}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
