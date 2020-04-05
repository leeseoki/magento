<?php
/**
 * Webkul MobRepository Model
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */


namespace Webkul\Odoomagentoconnect\Model;

use Webkul\Odoomagentoconnect\Api\MobRepositoryInterface;
use Webkul\Odoomagentoconnect\Api\Data\CategoryInterface;
use Webkul\Odoomagentoconnect\Api\Data\CategoryInterfaceFactory;

/**
 * Defines the implementation class of the calculator service contract.
 */
class MobRepository implements MobRepositoryInterface
{
    
    /**
     * @var MobFactory
     */
    protected $mobRepositoryFactory;

    /**
     * Constructor.
     *
     * @param MobFactory
     */
    public function __construct(
        MobRepositoryFactory $mobRepositoryFactory,
        \Webkul\Odoomagentoconnect\Model\Category $categorymodel,
        \Webkul\Odoomagentoconnect\Model\Product $productmodel,
        \Magento\Catalog\Model\Category $catalogCategory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_categorymodel = $categorymodel;
        $this->_productmodel = $productmodel;
        $this->_catalogCategory = $catalogCategory;
        $this->mobRepositoryFactory = $mobRepositoryFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create product
     *
     * @param \Webkul\Odoomagentoconnect\Api\Data\CategoryInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function categoryMap(\Webkul\Odoomagentoconnect\Api\Data\CategoryInterface $category)
    {
        $this->_categorymodel->setData($category->getData());
        $this->_categorymodel->save();
        return $this->_categorymodel->getId();
    }

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
    public function categoryUpdate($categoryId, $name)
    {
        $category = $this->_catalogCategory
                        ->setStoreId(0)
                        ->load($categoryId);
        $category->setName($name);
        $category->save();
        return true;
    }

    /**
     * Create Product Mapping
     *
     * @param \Webkul\Odoomagentoconnect\Api\Data\ProductInterface $product
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function productMap(\Webkul\Odoomagentoconnect\Api\Data\ProductInterface $product)
    {
        $this->_productmodel->setData($product->getData());
        $this->_productmodel->save();
        return $this->_productmodel->getId();
    }
}
