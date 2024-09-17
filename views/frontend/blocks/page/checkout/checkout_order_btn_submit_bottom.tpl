[{if $oViewConf|method_exists:'checkAdyenHealth' && $oViewConf->checkAdyenHealth() && $payment->showInOrderCtrl()}]
    [{* We include it as template, so that it can be modified in custom themes *}]
    [{include file="modules/osc/adyen/payment/adyen_assets.tpl"}]

    [{if $payment->isAdyenCreditCardPayment()}]
        [{$smarty.block.parent}]
    [{/if}]

    [{include file="modules/osc/adyen/payment/adyen_order_submit.tpl"}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]
