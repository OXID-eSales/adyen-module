[{if $paymentReturnReason}]
    <div class="alert alert-danger" data-adyen-return-code="[{$paymentResultCode}]">
        [{oxmultilang ident='OSC_ADYEN_RETURN_NOT_SUCCESSFUL'}] [{oxmultilang ident=$paymentReturnReason}]
    </div>
[{/if}]
[{$smarty.block.parent}]
