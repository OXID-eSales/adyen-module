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
final class UserHistoryCest extends BaseCest
{
    use OrderHistory;


    protected UserOrderHistory $orderHistoryPage;
    protected $orderNumber;
    protected $placeholderPaymentId;

    protected $placeholder = [
        Module::PAYMENT_CREDITCARD_ID,
        Module::PAYMENT_PAYPAL_ID
    ];

    protected function _getOXID(): array
    {
        return [$this->placeholderPaymentId];
    }

    protected function _getPaymentId(): string
    {
        return "payment_" . $this->placeholderPaymentId;
    }

    public function _before(AcceptanceTester $I): void
    {
        $this->_setAcceptance($I);
    }

    public function _after(AcceptanceTester $I): void
    {
    }

    protected function _initializeDatabase($payment)
    {
        $I = $this->_getAcceptance();

        // activate payment method
        $I->updateInDatabase(
            'oxpayments',
            ['OXACTIVE' => 1],
            ['OXID' => $payment]
        );

        // assign country to payment method
        $I->haveInDatabase(
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
        $I->haveInDatabase(
            'oxuserbaskets',
            [
                'OXID' => 'basket_' . $payment,
                'OXUSERID' => Fixtures::get('userId'),
                'OXTITLE' => 'basket_' . $payment,
            ]
        );

        // put item into basket
        $I->haveInDatabase(
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

        $I->haveInDatabase(
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
        $I->haveInDatabase(
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
        $I = $this->_getAcceptance();
        $homePage = $I->openShop();
        $homePage->loginUser(Fixtures::get('userName'), Fixtures::get('userPassword'));

        $homePage->switchLanguage('.en');
    }

    public function checkUserHistoryAfterPaypalPayment(AcceptanceTester $I)
    {
        $this->placeholderPaymentId = Module::PAYMENT_PAYPAL_ID;
        $this->_initializeDatabase(Module::PAYMENT_PAYPAL_ID);
        $this->_initializeTest();

        $this->checkOrderHistory($I);
    }

    public function checkUserHistoryAfterCreditcardPayment(AcceptanceTester $I)
    {
        $this->placeholderPaymentId = Module::PAYMENT_CREDITCARD_ID;
        $this->_initializeDatabase(Module::PAYMENT_CREDITCARD_ID);
        $this->_initializeTest();

        $this->checkOrderHistory($I);
    }
}
