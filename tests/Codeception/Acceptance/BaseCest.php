<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Page\Checkout\ThankYou;
use OxidEsales\Codeception\Page\Page;
use OxidEsales\Codeception\Step\Basket as BasketSteps;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;

abstract class BaseCest
{
    private int $amount = 1;
    private AcceptanceTester $I;
    private Page $paymentSelection;

    public function _before(AcceptanceTester $I): void
    {
        foreach ($this->_getOXID() as $payment) {
            $I->updateInDatabase(
                'oxpayments',
                ['OXACTIVE' => 1],
                ['OXID' => $payment]
            );

            $I->haveInDatabase(
                'oxobject2payment',
                ['OXID' => 'test' . $payment,
                    'OXOBJECTID' => 'a7c40f631fc920687.20179984',
                    'OXPAYMENTID' => $payment,
                    'OXTYPE' => 'oxcountry'
                ]
            );
        }

        $this->I = $I;
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->clearShopCache();
    }

    /**
     * @return void
     */
    protected function _initializeTest()
    {
        $this->I->openShop();

        $basketItem = Fixtures::get('product');
        $basketSteps = new BasketSteps($this->I);
        $basketSteps->addProductToBasket($basketItem['oxid'], $this->amount);

        $homePage = $this->I->openShop();
        $homePage->loginUser(Fixtures::get('userName'), Fixtures::get('userPassword'));

        $homePage->openMiniBasket();
        $this->I->waitForDocumentReadyState();
        $this->paymentSelection = $homePage->openCheckout();
    }

    /**
     * @param string $label
     * @return Page
     */
    protected function _choosePayment(): Page
    {
        $label = "//label[@for='" . $this->_getPaymentId() . "']";

        $this->I->waitForElement($label);
        $this->I->click($label);

        return $this->paymentSelection->goToNextStep();
    }

    protected function _fillCreditCardDetails(): Page
    {
        $iframeCreditCardNumber = '.adyen-checkout__card__cardNumber__input iframe';
        $inputCreditCardNumber = '[data-fieldtype="encryptedCardNumber"]';

        $iframeCreditCardDate = '.adyen-checkout__card__exp-date__input iframe';
        $inputValidationDate = '[data-fieldtype="encryptedExpiryDate"]';

        $iframeCreditCardCVC = '.adyen-checkout__card__cvc__input iframe';
        $inputCVC = '[data-fieldtype="encryptedSecurityCode"]';

        $inputName = '[name="holderName"]';

        $this->I->waitForElement($iframeCreditCardNumber, 60);
        $this->I->switchToIFrame($iframeCreditCardNumber);
        $this->I->fillField($inputCreditCardNumber, $_ENV['CREDITCARDNUMBER']);
        $this->I->switchToIFrame();

        $this->I->switchToIFrame($iframeCreditCardDate);
        $this->I->fillField($inputValidationDate, $_ENV['CREDITCARDDATE']);
        $this->I->switchToIFrame();

        $this->I->switchToIFrame($iframeCreditCardCVC);
        $this->I->fillField($inputCVC, $_ENV['CREDITCARDCVC']);
        $this->I->switchToIFrame();


        $this->I->fillField($inputName, $_ENV['CREDITCARDNAME']);
        return $this->paymentSelection->goToNextStep();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function _checkSuccessfulPayment()
    {
        $this->I->waitForPageLoad();
        $thankYouPage = new ThankYou($this->I);
        $orderNumber = $thankYouPage->grabOrderNumber();
        return $orderNumber;
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    protected function _setAcceptance(AcceptanceTester $I)
    {
        $this->I = $I;
    }

    /**
     * @return AcceptanceTester
     */
    protected function _getAcceptance(): AcceptanceTester
    {
        return $this->I;
    }

    /**
     * @return string price of order
     */
    protected function _getPrice(): string
    {
        $basketItem = Fixtures::get('product');
        return Registry::getLang()->formatCurrency(
            $basketItem['bruttoprice_single'] * $this->amount + $basketItem['shipping_cost']
        );
    }

    /**
     * @return string currency
     */
    protected function _getCurrency(): string
    {
        $basketItem = Fixtures::get('product');
        return $basketItem['currency'];
    }

    abstract protected function _getOXID(): array;

    abstract protected function _getPaymentId(): string;
}
