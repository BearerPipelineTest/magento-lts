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
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating Observer Model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Observer
{
    /**
     * Cleanup product ratings after product delete
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Rating_Model_Observer
     */
    public function processProductAfterDeleteEvent(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $eventProduct */
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            Mage::getResourceSingleton('rating/rating')->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
