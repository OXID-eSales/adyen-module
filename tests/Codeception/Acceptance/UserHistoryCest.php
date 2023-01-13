<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\_support\Traits\OrderHistory;

/**
 * @group UserHistory
 * @group osc_adyen
 */
final class UserHistoryCest
{
    use OrderHistory;

    protected AcceptanceTester $I;
    protected UserOrderHistory $orderHistoryPage;
    protected $orderNumber;
    protected $placeholderPaymentMethod;

    protected $placeholder = [
        Module::PAYMENT_CREDITCARD_ID => 'OSC_ADYEN_PAYMENT_METHOD_CREDITCARD',
        Module::PAYMENT_PAYPAL_ID => 'OSC_ADYEN_PAYMENT_METHOD_PAYPAL'
    ];

    public function _before(AcceptanceTester $I): void
    {
        $this->I = $I;
    }

    public function _after(AcceptanceTester $I): void
    {
    }

    protected function _initializeDatabase($payment)
    {
        // activate payment method
        $this->I->updateInDatabase(
            'oxpayments',
            ['OXACTIVE' => 1],
            ['OXID' => $payment]
        );

        // assign country to payment method
        $this->I->haveInDatabase(
            'oxobject2payment',
            ['OXID' => 'test' . $payment,
                'OXOBJECTID' => 'a7c40f631fc920687.20179984',
                'OXPAYMENTID' => $payment,
                'OXTYPE' => 'oxcountry'
            ]
        );

        $now = date('Y-m-d');
        $basketItem = Fixtures::get('product');

        // create a user basket
        $this->I->haveInDatabase(
            'oxuserbaskets',
            [
                'OXID' => 'basket_' . $payment,
                'OXUSERID' => Fixtures::get('userId'),
                'OXTITLE' => 'basket_' . $payment,
            ]
        );

        // put item into basket
        $this->I->haveInDatabase(
            'oxuserbasketitems',
            [
                'OXID' => 'item_' . $payment,
                'OXBASKETID' => 'basket_' . $payment,
                'OXARTID' => $basketItem['oxid'],
                'OXAMOUNT' => 1,
                'OXPERSPARAM' => ''
            ]
        );

        // set up the order for the basket
        $this->orderNumber = 4711;

        $this->I->haveInDatabase(
            'oxorder',
            [
                'OXID' => 'order_' . $payment,
                'OXSHOPID' => 1,
                'OXUSERID' => Fixtures::get('userId'),
                'OXORDERDATE' => $now,
                'OXBILLDATE' => $now,
                'OXSENDDATE' => $now,
                'OXPAID' => $now,
                'OXCARDTEXT' => '---',
                'OXREMARK' => '---',
                'OXPAYMENTID' => 'payment_' . $payment,
                'OXPAYMENTTYPE' => $payment,
                'OXORDERNR' => $this->orderNumber,
                'ADYENPSPREFERENCE' => $this->orderNumber
            ]
        );

        // fake the payment, so it appears in the order history
        $this->I->haveInDatabase(
            'oxuserpayments',
            [
                'OXID' => 'payment_' . $payment,
                'OXUSERID' => Fixtures::get('userId'),
                'OXPAYMENTSID' => $payment,
                'OXVALUE' => $this->orderNumber
                ]
        );
    }

    protected function _initializeTest()
    {
        $homePage = $this->I->openShop();
        $homePage->loginUser(Fixtures::get('userName'), Fixtures::get('userPassword'));

        $this->orderHistoryPage = new UserOrderHistory($this->I);
        $this->I->amOnPage($this->orderHistoryPage->URL);
    }

    public function checkUserHistoryAfterPaypalPayment(AcceptanceTester $I)
    {
        $paymentType = Module::PAYMENT_PAYPAL_ID;
        $this->_initializeDatabase($paymentType);
        $this->_initializeTest();

        $this->placeholderPaymentMethod = $this->placeholder[ $paymentType ];
        $this->checkOrderHistory($I);
    }

    public function checkUserHistoryAfterCreditcardPayment(AcceptanceTester $I)
    {
        $paymentType = Module::PAYMENT_CREDITCARD_ID;
        $this->_initializeDatabase($paymentType);
        $this->_initializeTest();

        $this->placeholderPaymentMethod = $this->placeholder[ $paymentType ];
        $this->checkOrderHistory($I);
    }
}
