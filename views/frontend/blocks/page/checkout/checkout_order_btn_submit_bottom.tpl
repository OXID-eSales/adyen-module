[{if $oViewConf|method_exists:'checkAdyenHealth' && $oViewConf->checkAdyenHealth() && $payment->showInOrderCtrl()}]
    [{* We include it as template, so that it can be modified in custom themes *}]
    [{include file="modules/osc/adyen/payment/adyen_assets.tpl"}]

    [{if $payment->isAdyenCreditCardPayment()}]
        [{assign var="payment" value=$oView->getPayment()}]
        [{$payment->oxpayments__oxdesc->value}]
        <div id="oscadyencreditcard-container" data-paymentid="payment_oscadyencreditcard">
        </div>

        <div class="">
            <button type="submit" class="adyen-checkout__button adyen-checkout__button--pay btn-lg pull-right largeButton">
                <i class="fa fa-check"></i> [{oxmultilang ident="SUBMIT_ORDER"}]
            </button>
        </div>
    [{/if}]

    [{include file="modules/osc/adyen/payment/adyen_order_submit.tpl"}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]
