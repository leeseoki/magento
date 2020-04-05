<?php
/**
 * Webkul Odoomagentoconnect Product SyncProduct Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class SyncProduct extends \Magento\Catalog\Controller\Adminhtml\Product
{
        /**
         * @param Action\Context $context
         * @param Builder $productBuilder
         * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
         * @param Filter $filter
         * @param CollectionFactory $collectionFactory
         */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Webkul\Odoomagentoconnect\Model\Product $productMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Product $productModel,
        \Magento\Catalog\Model\Product $catalogModel,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->filter = $filter;
        $this->_connection = $connection;
        $this->_catalogModel = $catalogModel;
        $this->_productModel = $productModel;
        $this->_productMapping = $productMapping;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $productBuilder);
    }

    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId) {
            $countNonSyncProduct = 0;
            $countSyncProduct = 0;
            $alreadySyncProduct = 0;
            $countUpdateProduct = 0;
            $countNonUpdateProduct = 0;
            $errorMessage = '';
            $errorUpdateMessage = '';
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productIds = $collection->getAllIds();
            foreach ($collection as $product) {
                $productId = $product->getId();
                $mapping = $this->_productMapping->getCollection()
                                                ->addFieldToFilter('magento_id', ['eq'=>$productId]);
                $productObj = $this->_productModel;
                if ($mapping->getSize() == 0) {
                    $response = $productObj->createSpecificProduct($productId);
                    if ($response['odoo_id'] > 0) {
                        $countSyncProduct++;
                    } else {
                        $countNonSyncProduct++;
                        $errorMessage = $response['error'];
                    }
                } else {
                    foreach ($mapping as $mageObj) {
                        if ($mageObj->getNeedSync() == "yes") {
                            $response = $productObj->updateNormalProduct($mageObj);
                            if ($response['odoo_id'] > 0) {
                                $countUpdateProduct++;
                            } else {
                                $countNonUpdateProduct++;
                                $errorUpdateMessage = $response['error'];
                            }
                        } else {
                            $alreadySyncProduct++;
                        }
                    }
                }
            }

            if ($countNonSyncProduct) {
                $this->messageManager->addError(
                    __(
                        '%1 product(s) cannot be synchronized at Odoo. Reason : '.$errorMessage,
                        $countNonSyncProduct
                    )
                );
            }
            if ($countSyncProduct) {
                $this->messageManager
                    ->addSuccess(
                        __(
                            'Total of %1 product(s) have been successfully Exported at Odoo.',
                            $countSyncProduct
                        )
                    );
            }
            if ($countUpdateProduct) {
                $this->messageManager
                    ->addSuccess(
                        __(
                            'Total of %1 product(s) have been successfully Updated at Odoo.',
                            $countUpdateProduct
                        )
                    );
            }
            if ($countNonUpdateProduct) {
                $this->messageManager->addError(
                    __(
                        '%1 product(s) cannot be update at Odoo. Reason : '.$errorUpdateMessage,
                        $countNonUpdateProduct
                    )
                );
            }
            if ($alreadySyncProduct) {
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 product(s) are already Synchronized at Odoo.',
                        $alreadySyncProduct
                    )
                );
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "Selected product(s) cannot be synchronized at Odoo. !! Reason : ".$errorMessage
                )
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return  $resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
    }
}
