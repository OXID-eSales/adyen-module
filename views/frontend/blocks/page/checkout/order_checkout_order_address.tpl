[{if $paymentReturnReason}]
    <div class="alert alert-danger">
        [{oxmultilang ident='OSC_ADYEN_RETURN_NOT_SUCCESSFUL'}] [{oxmultilang ident=$paymentReturnReason}]
    </div>
[{/if}]
[{$smarty.block.parent}]
