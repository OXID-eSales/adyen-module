<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.js"
        integrity="[{$oViewConf->getAdyenIntegrityJS()}]"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.css"
      integrity="[{$oViewConf->getAdyenIntegrityCSS()}]"
      crossorigin="anonymous">
<style>
    /* Visual gate for redirect-based Adyen payment buttons (Twint, Klarna).
       While the AGB checkbox(es) are not confirmed, the rendered Adyen
       button is dimmed and not clickable; otherwise the shopper would leave
       the page before OXID's AGB check ever runs. Inline because the module
       has no CSS pipeline that emits a loaded stylesheet today. */
    .osc-adyen-redirect-blocked {
        position: relative;
        opacity: 0.5;
        pointer-events: none;
        filter: grayscale(0.6);
    }
    .osc-adyen-redirect-blocked::after {
        content: "";
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        cursor: not-allowed;
    }
    @keyframes osc-adyen-agb-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(217, 83, 79, 0.6); }
        70%  { box-shadow: 0 0 0 12px rgba(217, 83, 79, 0); }
        100% { box-shadow: 0 0 0 0 rgba(217, 83, 79, 0); }
    }
    .osc-adyen-agb-missing {
        outline: 2px solid #d9534f;
        outline-offset: 2px;
        animation: osc-adyen-agb-pulse 1.2s ease-out 2;
    }
</style>
[{assign var="sToken" value=$oViewConf->getSessionChallengeToken()}]
[{assign var="sSelfLink" value=$oViewConf->getSslSelfLink()|replace:"&amp;":"&"}]
[{assign var="adyenCreditCard" value=$oViewConf->getAdyenPaymentCreditCardId()}]
[{assign var="adyenPayPal" value=$oViewConf->getAdyenPaymentPayPalId()}]
[{assign var="adyenGooglePay" value=$oViewConf->getAdyenPaymentGooglePayId()}]
[{assign var="adyenApplePay" value=$oViewConf->getAdyenPaymentApplePayId()}]
[{assign var="isPaymentPage" value=false}]
[{assign var="isOrderPage" value=false}]
[{if $oViewConf->getTopActiveClassName() == 'payment'}]
    [{assign var="isPaymentPage" value=true}]
    [{assign var="paymentID" value=$oView->getCheckedPaymentId()}]
[{elseif $oViewConf->getTopActiveClassName() == 'order'}]
    [{assign var="isOrderPage" value=true}]
    [{assign var="paymentID" value=$payment->getId()}]
[{/if}]
[{if $isOrderPage && $paymentID == $adyenGooglePay}]
    <script src="https://pay.google.com/gp/p/js/pay.js"></script>
[{/if}]
[{if $phpStorm}]<script>[{/if}]
    [{capture assign="adyenJS"}]
        [{assign var="isLog" value=$oViewConf->isAdyenLoggingActive()}]
        [{assign var="templateCheckoutCreateId" value=$oViewConf->getTemplateCheckoutCreateId($payment)}]
        [{assign var="templatePayButtonContainerId" value=$oViewConf->getTemplatePayButtonContainerId($payment)}]
        let submitForm, submitLink;
        [{if $isPaymentPage}]
            submitForm = document.getElementById('payment');
            submitLink = document.getElementById('orderStep');
            const nextStepEl = document.getElementById('paymentNextStepBottom');

            // prevent submit by clicking 'orderStep'-Link -> remove javascript-href from original template and add own click event
            submitLink.href = "#";
            submitLink.addEventListener('click', function () {
                nextStepEl.click();
            });

            [{* reset the disabled-status of paymentNextStepBottom if payment is changed *}]
            document.getElementsByName('paymentid').forEach(function (e) {
                e.addEventListener('change', function () {
                    nextStepEl.disabled = false;
                    nextStepEl.dataset.adyensubmit = '';
                });
            });
        [{elseif $isOrderPage}]
            submitForm = document.getElementById('orderConfirmAgbBottom');
        [{/if}]
        const adyenPspReferenceEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamPspReferenceName()}]');
        const adyenResultCodeEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamResultCodeName()}]');
        const adyenAmountCurrencyEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]');
        const adyenAmountValueEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamAmountValueName()}]');
        const orderSubmitButton = document.querySelector("#orderConfirmAgbBottom button");

        const adyenAsync = async function () {
            [{$oViewConf->getTemplateConfiguration($oView, $payment)}]

            const checkout = await AdyenCheckout(configuration);
            // Access the available payment methods for the session.
            [{if $isLog}]
                console.log(checkout.paymentMethodsResponse);
            [{/if}]
            [{if $isPaymentPage}]
                [{if $oView->handleAdyenAssets($adyenApplePay)}]
                    const apple = checkout.create(
                        'applepay',
                        {
                        }
                    );
                    apple.isAvailable()
                        .then(() => {  })
                        .catch(e => {
                            [{if $isLog}]
                                console.error('Apple Pay not available', e);
                            [{/if}]
                            const parentElement = document.getElementById('payment_[{$adyenApplePay}]').closest('.well.well-sm');
                            if (parentElement) {
                                parentElement.remove(); // remove parent-block
                            }
                        });
                [{/if}]
            [{elseif $isOrderPage}]
                [{if $orderPaymentIsRedirect}]
                    // Keep the redirect-payment button container in sync with the AGB checkbox(es).
                    // Adyen renders a fully clickable button on mount, so we wrap it with a CSS
                    // class that intercepts pointer events whenever the agreements are missing.
                    const oscAdyenRedirectContainer = document.getElementById('[{$templatePayButtonContainerId}]');
                    const oscAdyenSyncRedirectGuard = function () {
                        if (!oscAdyenRedirectContainer) {
                            return;
                        }
                        const agreements = oscAdyenReadAgreements();
                        if (agreements === null) {
                            return;
                        }
                        const blocked = (agreements.hasOrdAgb && !agreements.ordAgb)
                            || (agreements.hasDownloadable && !agreements.downloadable)
                            || (agreements.hasService && !agreements.service);
                        oscAdyenRedirectContainer.classList.toggle('osc-adyen-redirect-blocked', blocked);
                    };
                    ['checkAgbTop', 'oxdownloadableproductsagreement', 'oxserviceproductsagreement']
                        .forEach(function (id) {
                            const el = document.getElementById(id);
                            if (el) {
                                el.addEventListener('change', oscAdyenSyncRedirectGuard);
                                el.addEventListener('click', oscAdyenSyncRedirectGuard);
                            }
                        });
                    oscAdyenSyncRedirectGuard();
                [{/if}]
                [{if $orderPaymentCreditCard}]
                    orderSubmitButton.disabled = true;
                    orderSubmitButton.title = '[{assign var="template_title" value="OSC_ADYEN_ORDER_TOOLTIP"|oxmultilangassign}]';
                    const cardComponent = checkout.create(
                        'card',
                        {
                            onFieldValid : function() {
                                orderSubmitButton.disabled = false;
                            },
                            onLoad: function () {
                                document.querySelector("#oscadyencreditcard-container button").style.display = 'none';
                            }
                        }
                    ).mount('#[{$adyenCreditCard}]-container');
                [{elseif $orderPaymentApplePay}]
                    const applePayComponent = checkout.create('[{$templateCheckoutCreateId}]', configuration);
                        applePayComponent.isAvailable()
                            .then(() => {
                                [{if $isLog}]
                                    console.log('mount checkout component');
                                [{/if}]
                                applePayComponent.mount('#[{$templatePayButtonContainerId}]');
                            })
                            .catch(e => {
                                [{if $isLog}]
                                    console.error('Apple Pay not available', e);
                                [{/if}]
                            });
                    [{else}]
                        checkout.create('[{$templateCheckoutCreateId}]', configuration).mount('#[{$templatePayButtonContainerId}]');
                [{/if}]

                [{if $orderPaymentCreditCard}]
                    submitForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        this.disabled = true;
                        [{if $isLog}]
                            console.log("cardComp:", cardComponent)
                        [{/if}]
                        cardComponent.submit();
                    });
                [{/if}]
            [{/if}]

            const makePayment = (paymentRequest = {}) => {
                return httpPost('payments', paymentRequest)
                    .then(response => {
                        if (response.error) throw new Error('Payment initiation failed');
                        return response;
                    })
                    .catch(error => {
                        throw Error(error);
                    });
            };

            const makeDetailsCall = data =>
                httpPost('details', data)
                    .then(response => {
                        if (response.error || response.errorCode) throw new Error('Details call failed');
                        return response;
                    })
                    .catch(error => {
                        throw Error(error);
                    });

            const httpPost = (endpoint, data) =>
                fetch('[{$sSelfLink}]cl=adyenjscontroller&fnc=' + endpoint + '&stoken=[{$sToken}]', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json, text/plain, */*',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                }).then(response => response.json());

            const setPspReference = (response) => {
                var result = false;
                if (response.pspReference && response.resultCode === 'Authorised') {
                    adyenPspReferenceEl.value = response.pspReference;
                    adyenResultCodeEl.value = response.resultCode;
                    adyenAmountCurrencyEl.value = response.amount.currency;
                    adyenAmountValueEl.value = response.amount.value;
                    result = true;
                }
                else if (response.resultCode !== 'Authorised') {
                    window.location.replace('[{$sSelfLink}]cl=payment&payerror=2&stoken=[{$sToken}]');
                }
                if (result === true) {
                    if (typeof submitForm !== 'undefined') {
                        submitForm.submit();
                    }
                }
                return result;
            }
        }
        // Call adyenAsync
        adyenAsync();

    [{/capture}]
[{if $phpStorm}]</script>[{/if}]
[{oxscript add=$adyenJS}]
