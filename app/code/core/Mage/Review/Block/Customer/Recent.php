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
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recent Customer Reviews Block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @property Mage_Review_Model_Resource_Review_Product_Collection $_collection
 */
class Mage_Review_Block_Customer_Recent extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('review/customer/list.phtml');

        $this->_collection = Mage::getModel('review/review')->getProductCollection();

        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setDateOrder()
            ->setPageSize(5)
            ->load()
            ->addReviewSummary();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * @return string
     */
    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    /**
     * @return string
     */
    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * @return string
     */
    public function getAllReviewsUrl()
    {
        return Mage::getUrl('review/customer');
    }

    /**
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return Mage::getUrl('review/customer/view', ['id' => $id]);
    }
}
