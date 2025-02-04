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
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customers collection
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Customer_Model_Address getItemById(int $value)
 * @method Mage_Customer_Model_Address[] getItems()
 */
class Mage_Customer_Model_Resource_Address_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('customer/address');
    }

    /**
     * Set customer filter
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function setCustomerFilter($customer)
    {
        if ($customer->getId()) {
            $this->addAttributeToFilter('parent_id', $customer->getId());
        } else {
            $this->addAttributeToFilter('parent_id', '-1');
        }
        return $this;
    }
}
