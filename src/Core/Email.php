<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

/**
 * Confirmation mails for refunds and order cancellations triggered in the
 * backend. Rendering and recipient handling only; whether a mail is sent at all
 * is decided by RefundMailService, which is the single caller.
 *
 * @mixin \OxidEsales\Eshop\Core\Email
 */
class Email extends Email_parent
{
    /**
     * Refund confirmation - HTML
     *
     * @var string
     */
    protected $adyenRefundTplHtml = "modules/osc/adyen/email/html/refund.tpl";

    /**
     * Refund confirmation - Plain
     *
     * @var string
     */
    protected $adyenRefundTplPlain = "modules/osc/adyen/email/plain/refund.tpl";

    /**
     * Cancellation confirmation - HTML
     *
     * @var string
     */
    protected $adyenCancelTplHtml = "modules/osc/adyen/email/html/cancel.tpl";

    /**
     * Cancellation confirmation - Plain
     *
     * @var string
     */
    protected $adyenCancelTplPlain = "modules/osc/adyen/email/plain/cancel.tpl";
    /**
     * @param Order $order
     * @param float $refundedAmount amount Adyen confirmed as refunded
     * @param string $currency currency code of the refunded amount
     * @return bool
     */
    public function sendAdyenRefundMailToCustomer(
        Order $order,
        float $refundedAmount,
        string $currency
    ): bool {
        return $this->sendAdyenRefundMail($order, $refundedAmount, $currency, false);
    }

    /**
     * @param Order $order
     * @param float $refundedAmount amount Adyen confirmed as refunded
     * @param string $currency currency code of the refunded amount
     * @return bool
     */
    public function sendAdyenRefundMailToOwner(
        Order $order,
        float $refundedAmount,
        string $currency
    ): bool {
        return $this->sendAdyenRefundMail($order, $refundedAmount, $currency, true);
    }

    /**
     * @param Order $order
     * @param float|null $refundedAmount amount refunded along with the
     *                                   cancellation, null if no refund was made
     * @param string $currency currency code of the refunded amount
     * @return bool
     */
    public function sendAdyenCancelMailToCustomer(
        Order $order,
        ?float $refundedAmount,
        string $currency
    ): bool {
        return $this->sendAdyenCancelMail($order, $refundedAmount, $currency, false);
    }

    /**
     * @param Order $order
     * @param float|null $refundedAmount amount refunded along with the
     *                                   cancellation, null if no refund was made
     * @param string $currency currency code of the refunded amount
     * @return bool
     */
    public function sendAdyenCancelMailToOwner(
        Order $order,
        ?float $refundedAmount,
        string $currency
    ): bool {
        return $this->sendAdyenCancelMail($order, $refundedAmount, $currency, true);
    }

    /**
     * @param Order $order
     * @param float $refundedAmount
     * @param string $currency
     * @param bool $toOwner send to the shop owner instead of the customer
     * @return bool
     */
    protected function sendAdyenRefundMail(
        Order $order,
        float $refundedAmount,
        string $currency,
        bool $toOwner
    ): bool {
        return $this->sendAdyenOrderMail(
            $order,
            $toOwner,
            $this->adyenRefundTplHtml,
            $this->adyenRefundTplPlain,
            $toOwner ? 'OSC_ADYEN_REFUND_MAIL_SUBJECT_OWNER' : 'OSC_ADYEN_REFUND_MAIL_SUBJECT',
            [
                'adyenRefundedAmount' => $refundedAmount,
                'adyenCurrencyCode' => $currency,
            ]
        );
    }

    /**
     * @param Order $order
     * @param float|null $refundedAmount
     * @param string $currency
     * @param bool $toOwner send to the shop owner instead of the customer
     * @return bool
     */
    protected function sendAdyenCancelMail(
        Order $order,
        ?float $refundedAmount,
        string $currency,
        bool $toOwner
    ): bool {
        return $this->sendAdyenOrderMail(
            $order,
            $toOwner,
            $this->adyenCancelTplHtml,
            $this->adyenCancelTplPlain,
            $toOwner ? 'OSC_ADYEN_CANCEL_MAIL_SUBJECT_OWNER' : 'OSC_ADYEN_CANCEL_MAIL_SUBJECT',
            [
                'adyenRefundedAmount' => $refundedAmount,
                'adyenCurrencyCode' => $currency,
            ]
        );
    }

    /**
     * @param Order $order
     * @param bool $toOwner
     * @param string $htmlTemplate
     * @param string $plainTemplate
     * @param string $subjectIdent language ident, receives the order number
     * @param array<string, mixed> $viewData additional template variables
     * @return bool
     */
    protected function sendAdyenOrderMail(
        Order $order,
        bool $toOwner,
        string $htmlTemplate,
        string $plainTemplate,
        string $subjectIdent,
        array $viewData
    ): bool {
        // The customer is written to in the language they ordered in. The shop owner
        // keeps the language the backend is running in, because that copy is read
        // next to the order there - so only the customer mail switches the language.
        // Core\Email::sendSendedNowMail() handles its backend triggered mail the
        // same way, including loading the shop in that language: the shop name and
        // the sender texts are translatable too.
        $mailLanguage = $toOwner ? null : $this->adyenOrderLanguage($order);

        $shop = $mailLanguage === null ? $this->_getShop() : $this->_getShop($mailLanguage);
        $this->_setMailParams($shop);

        $this->setViewData('order', $order);
        $this->setViewData('currency', $order->getOrderCurrency());
        $this->setViewData('isAdyenOwnerMail', $toOwner);
        foreach ($viewData as $name => $value) {
            $this->setViewData($name, $value);
        }

        $renderer = $this->getRenderer();

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $lang = Registry::getLang();
        $previousTplLanguage = (int)$lang->getTplLanguage();
        $previousBaseLanguage = (int)$lang->getBaseLanguage();
        if ($mailLanguage !== null) {
            $lang->setTplLanguage($mailLanguage);
            $lang->setBaseLanguage($mailLanguage);
        }

        // These mails are triggered from the backend, but they use frontend
        // templates and frontend language files. Rendering them in admin mode
        // leaves core idents unresolved ("ERROR: Translation for ORDER_NUMBER not
        // found!"), so switch the admin mode off around the rendering and restore
        // whatever it was before.
        $config = Registry::getConfig();
        $wasAdmin = $config->isAdmin();
        $config->setAdminMode(false);

        try {
            $this->setBody($renderer->renderTemplate($htmlTemplate, $this->getViewData()));
            $this->setAltBody($renderer->renderTemplate($plainTemplate, $this->getViewData()));

            // the subject ident lives in the frontend language files, so it belongs
            // into the same window as the templates
            /** @var string $subject */
            $subject = $lang->translateString($subjectIdent);
            $this->setSubject(sprintf($subject, $this->adyenFieldAsString($order, 'oxordernr')));
        } finally {
            // A failing template must not leave the shop behind in frontend mode or
            // in the order language: the admin page that triggered the mail is
            // rendered after this and would lose its templates and translations.
            $config->setAdminMode($wasAdmin);
            if ($mailLanguage !== null) {
                $lang->setTplLanguage($previousTplLanguage);
                $lang->setBaseLanguage($previousBaseLanguage);
            }
        }

        if ($toOwner) {
            $this->setRecipient(
                $this->adyenFieldAsString($shop, 'oxowneremail'),
                $shop->oxshops__oxname->getRawValue()
            );

            return $this->send();
        }

        $fullName = $order->oxorder__oxbillfname->getRawValue()
            . ' ' . $order->oxorder__oxbilllname->getRawValue();

        $this->setRecipient($this->adyenFieldAsString($order, 'oxbillemail'), $fullName);
        $this->setReplyTo(
            $this->adyenFieldAsString($shop, 'oxorderemail'),
            $shop->oxshops__oxname->getRawValue()
        );

        return $this->send();
    }

    /**
     * Language the order was placed in. getFieldData() is untyped, so anything
     * that is not a number falls back to the shop default language.
     *
     * @param Order $order
     * @return int
     */
    protected function adyenOrderLanguage(Order $order): int
    {
        $language = $order->getFieldData('oxlang');

        return is_numeric($language) ? (int)$language : 0;
    }

    /**
     * getFieldData() is untyped, so anything that is not a plain value yields an
     * empty string instead of being cast.
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $model
     * @param string $field
     * @return string
     */
    protected function adyenFieldAsString($model, string $field): string
    {
        $value = $model->getFieldData($field);

        return is_scalar($value) ? (string)$value : '';
    }
}
