[{if $oViewConf|method_exists:'checkAdyenHealth' && $oViewConf->checkAdyenHealth() && $payment->getId() == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_PAYPAL_ID')}]
    [{* We include it as template, so that it can be modified in custom themes *}]
    [{include file="modules/osc/adyen/payment/adyen_order_submit.tpl"}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]