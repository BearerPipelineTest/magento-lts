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

namespace Mage\Catalog\Test\Block\Msrp;

use Magento\Mtf\Block\Block;

/**
 * MSRP popup block.
 */
class Popup extends Block
{
    /**
     * Price box selector.
     *
     * @var string
     */
    protected $priceBox = '.price-box';

    /**
     * Escape currency and separator for price.
     *
     * @param string $price
     * @param string $currency
     * @return string
     */
    protected function escape($price, $currency = '$')
    {
        return str_replace($currency, '', $price);
    }

    /**
     * Get MAP text.
     *
     * @return string
     */
    public function getMap()
    {
        return $this->escape($this->_rootElement->find($this->priceBox)->getText());
    }

    /**
     * Check if price visible in popup dialog.
     *
     * @return bool
     */
    public function isPriceVisible()
    {
        return $this->_rootElement->find($this->priceBox)->isVisible();
    }
}
