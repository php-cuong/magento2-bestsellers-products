<?php
/**
 * GiaPhuGroup Co., Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GiaPhuGroup.com license that is
 * available through the world-wide-web at this URL:
 * https://www.giaphugroup.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    PHPCuong
 * @package     PHPCuong_BestsellersProducts
 * @copyright   Copyright (c) 2018-2019 GiaPhuGroup Co., Ltd. All rights reserved. (http://www.giaphugroup.com/)
 * @license     https://www.giaphugroup.com/LICENSE.txt
 */

namespace PHPCuong\BestsellersProducts\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Join sales_bestsellers_aggregated_yearly relation table to retrieve the bestseller products
     *
     * @param int $storeId
     * @return $this
     */
    public function getBestsellersProduct($storeId)
    {
        $this->getSelect()->join(
            ['bestseller' => $this->getTable('sales_bestsellers_aggregated_yearly')],
            'e.entity_id = bestseller.product_id',
            ['SUM(bestseller.qty_ordered) as qty_ordered']
        )->where('bestseller.store_id = ?', (int)$storeId)->group('bestseller.product_id')->order('qty_ordered DESC');

        //$this->printLogQuery(false, true);
        return $this;
    }
}
