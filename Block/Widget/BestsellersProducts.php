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

namespace PHPCuong\BestsellersProducts\Block\Widget;

class BestsellersProducts extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \PHPCuong\BestsellersProducts\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $catalogProductStatus;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var string
     */
    protected $_template = 'PHPCuong_BestsellersProducts::widget/bestsellers-products.phtml';

    /**
     * NewWidget constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \PHPCuong\BestsellersProducts\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \PHPCuong\BestsellersProducts\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductStatus = $catalogProductStatus;
        $this->catalogProductVisibility = $catalogProductVisibility;
    }

    /**
     * @return \PHPCuong\BestsellersProducts\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create()
            // Add all the product attributes to select
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', ['in' => $this->catalogProductVisibility->getVisibleInCatalogIds()])
            // Filtering the products, only get the products have the status allowed
            ->addAttributeToFilter('status', ['in' => $this->catalogProductStatus->getVisibleStatusIds()]);

        $qty = (int)$this->getQty();
        // set the default Qty if the qty doesn't exist
        if (!$qty) {
            $qty = 25;
        }

        // get the current store id
        $storeId = (int)$this->_storeManager->getStore()->getId();
        $collection = $collection->getBestsellersProduct($storeId)->setPageSize($qty)->setCurPage(1);

        return $collection;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

            /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
