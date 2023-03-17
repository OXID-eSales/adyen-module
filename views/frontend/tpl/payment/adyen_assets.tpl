<script src="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.js"
        integrity="[{$oViewConf->getAdyenIntegrityJS()}]"
        crossorigin="anonymous"></script>
<link rel="stylesheet"
      href="https://checkoutshopper-[{$oViewConf->getAdyenOperationMode()}].adyen.com/checkoutshopper/sdk/[{$oViewConf->getAdyenSDKVersion()}]/adyen.css"
      integrity="[{$oViewConf->getAdyenIntegrityCSS()}]"
      crossorigin="anonymous">
[{if $payment->oxpayments__oxid->value == constant('\OxidSolutionCatalysts\Adyen\Core\Module::PAYMENT_GOOGLE_PAY_ID')}]
    <script src="https://pay.google.com/gp/p/js/pay.js"></script>
[{/if}]
[{assign var="sToken" value=$oViewConf->getSessionChallengeToken()}]
[{assign var="sSelfLink" value=$oViewConf->getSslSelfLink()|replace:"&amp;":"&"}]
[{assign var="adyenCreditCard" value=$oViewConf->getAdyenPaymentCreditCardId()}]
[{assign var="adyenPayPal" value=$oViewConf->getAdyenPaymentPayPalId()}]
[{assign var="adyenGooglePay" value=$oViewConf->getAdyenPaymentGooglePayId()}]
[{if $phpStorm}]<script>[{/if}]
    [{capture assign="adyenJS"}]
        [{assign var="isLog" value=$oViewConf->isAdyenLoggingActive()}]
        [{assign var="isPaymentPage" value=false}]
        [{assign var="isOrderPage" value=false}]
        [{assign var="templateCheckoutCreateId" value=$oViewConf->getTemplateCheckoutCreateId($payment->getId())}]
        [{assign var="templatePayButtonContainerId" value=$oViewConf->getTemplatePayButtonContainerId($payment->getId())}]
        let submitForm, submitLink;
        [{if $oViewConf->getTopActiveClassName() == 'payment'}]
            [{assign var="isPaymentPage" value=true}]
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
        [{elseif $oViewConf->getTopActiveClassName() == 'order'}]
            [{assign var="isOrderPage" value=true}]
            [{assign var="paymentID" value=$payment->getId()}]
            submitForm = document.getElementById('orderConfirmAgbBottom');
        [{/if}]
        const adyenPspReferenceEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamPspReferenceName()}]');
        const adyenResultCodeEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamResultCodeName()}]');
        const adyenAmountCurrencyEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamAmountCurrencyName()}]');
        const adyenAmountValueEl = document.getElementById('[{$oViewConf->getAdyenHtmlParamAmountValueName()}]');

        const adyenAsync = async function () {
            [{$oViewConf->getTemplateConfiguration($oView, $payment)}]

            const checkout = await AdyenCheckout(configuration);
            // Access the available payment methods for the session.
            [{if $isLog}]
                console.log(checkout.paymentMethodsResponse);
            [{/if}]
            [{if $isPaymentPage && $oView->isAvailablePayment($adyenCreditCard)}]
                const cardComponent = checkout.create('[{$templateCheckoutCreateId}]').mount('#[{$templatePayButtonContainerId}]');
                cardComponent.paymentIdViewEl = undefined;
            [{elseif $isOrderPage}]
                [{if $orderPaymentApplePay}]
                    const applePayComponent = checkout.create('[{$templateCheckoutCreateId}]', configuration);
                        applePayComponent.isAvailable()
                            .then(() => {
                                [{if $isLog}]
                                    console.log('mount checkout component')
                                [{/if}]
                                applePayComponent.mount('#[{$templatePayButtonContainerId}]');
                            })
                            .catch(e => {
                                [{if $isLog}]
                                    console.error('Apple Pay not available')
                                    console.error(e)
                                [{/if}]
                            });
                    [{else}]
                        checkout.create('[{$templateCheckoutCreateId}]', configuration).mount('#[{$templatePayButtonContainerId}]');
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

            const setPaymentIdEl = (component, nextStepElDisabled) => {
                const paymentIdEl = document.getElementById(component._node.attributes.getNamedItem('data-paymentid').value);
                paymentIdEl.checked = true;
                nextStepEl.disabled = nextStepElDisabled;
                nextStepEl.dataset.adyensubmit = '';
                return paymentIdEl;
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
                fetch('[{$sSelfLink}]cl=adyenjscontroller&fnc=' + endpoint + '&stoken=[{$sToken}][{if $oViewConf->isAdyenSandboxMode()}]&XDEBUG_SESSION_START=1[{/if}]', {
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

            [{if $isPaymentPage}]
                nextStepEl.addEventListener("click", function(e) {
                    if (this.dataset.adyensubmit !== '') {
                        e.preventDefault();
                        this.disabled = true;
                        if (this.dataset.adyensubmit === '[{$adyenCreditCard}]') {
                            cardComponent.paymentIdViewEl = document.getElementById('payment_[{$adyenCreditCard}]').parentElement;
                            cardComponent.submit();
                        }
                    }
                }, false);
            [{/if}]
        }
        // Call adyenAsync
        adyenAsync();

    [{/capture}]
    [{if $phpStorm}]</script>[{/if}]
[{oxscript add=$adyenJS}]