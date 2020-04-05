<?php

namespace Webkul\Odoomagentoconnect\Api;

/**
 * @api
 */
interface MobRepositoryInterface
{
    /**
     * Create product
     *
     * @param \Webkul\Odoomagentoconnect\Api\Data\CategoryInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function categoryMap(\Webkul\Odoomagentoconnect\Api\Data\CategoryInterface $category);

    /**
     * Create product
     *
     * @param int $categoryId
     * @param string $name
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function categoryUpdate($categoryId, $name);

    /**
     * Create Product Mapping
     *
     * @param \Webkul\Odoomagentoconnect\Api\Data\ProductInterface $product
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function productMap(\Webkul\Odoomagentoconnect\Api\Data\ProductInterface $product);
}
