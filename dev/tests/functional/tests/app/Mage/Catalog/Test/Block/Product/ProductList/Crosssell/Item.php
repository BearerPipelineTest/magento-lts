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

namespace Mage\Catalog\Test\Block\Product\ProductList\Crosssell;

use Magento\Mtf\Block\Block;

/**
 * Item block on Crosssell product block.
 */
class Item extends Block
{
    /**
     * Add to cart button.
     *
     * @var string
     */
    protected $addToCart = ".btn-cart";

    /**
     * Add to cart crossSell product.
     *
     * @return void
     */
    public function addToCart()
    {
        $this->_rootElement->find($this->addToCart)->click();
    }
}
