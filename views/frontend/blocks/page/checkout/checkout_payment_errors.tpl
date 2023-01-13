[{if $oView->getPaymentError() === $oViewConf->getAdyenErrorInvalidSession()}]
    <div class="alert alert-danger" role="alert">
        [{oxmultilang ident="OSC_ADYEN_REAUTHNECESSARY"}]
    </div>
[{else}]
    [{$smarty.block.parent}]
[{/if}]
