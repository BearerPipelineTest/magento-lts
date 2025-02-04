<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Mage\Paypal\Test\Fixture\PaypalCustomer;
use Mage\Paypal\Test\Page\Paypal;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Paypal\Test\Block\AbstractReview;

/**
 * Continue Pay Pal checkout step.
 */
class ContinuePayPalCheckoutStep implements TestStepInterface
{
    /**
     * Pay Pal page.
     *
     * @var Paypal
     */
    protected $paypalPage;

    /**
     * One page checkout success page.
     *
     * @var CheckoutOnepageSuccess
     */
    protected $checkoutOnepageSuccess;

    /**
     * PayPal customer.
     *
     * @var PaypalCustomer
     */
    protected $customer;

    /**
     * Review block.
     *
     * @var AbstractReview
     */
    protected $reviewBlock;

    /**
     * @constructor
     * @param Paypal $paypalPage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param PaypalCustomer $paypalCustomer
     */
    public function __construct(
        Paypal $paypalPage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        PaypalCustomer $paypalCustomer
    ) {
        $this->paypalPage = $paypalPage;
        $this->checkoutOnepageSuccess = $checkoutOnepageSuccess;
        $this->customer = $paypalCustomer;
    }

    /**
     * Continue Pay Pal checkout.
     *
     * @return array|null
     */
    public function run()
    {
        $reviewBlockIsPresent = false;
        $sleepingTime = 0;
        while (!$reviewBlockIsPresent and $sleepingTime <= 60) {
            sleep(1);
            $reviewBlockIsPresent = $this->paypalPage->getReviewBlock()->isVisible()
                || $this->paypalPage->getOldReviewBlock()->isVisible();
            $sleepingTime++;
        }
        $this->reviewBlock = $this->paypalPage->getReviewBlock()->isVisible()
            ? $this->paypalPage->getReviewBlock()
            : $this->paypalPage->getOldReviewBlock();
        $this->selectCustomerAddress($this->customer);
        $this->reviewBlock->continueCheckout();
        $successBlock = $this->checkoutOnepageSuccess->getSuccessBlock();

        return ['orderId' => $successBlock->isVisible() ? $successBlock->getGuestOrderId() : null];
    }

    /**
     * Select customer address.
     *
     * @param PaypalCustomer $customer
     * @return void
     */
    protected function selectCustomerAddress(PaypalCustomer $customer)
    {
        if ($this->reviewBlock->checkChangeAddressAbility()) {
            $address = $customer->getDataFieldConfig('address')['source']->getAddresses()[0];
            $this->reviewBlock->getAddressesBlock()->selectAddress($address);
            $this->reviewBlock->waitLoader();
        }
    }
}
