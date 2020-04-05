<?php
/**
 * Webkul Odoomagentoconnect Product MassDisable Controller
 * @Product  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDisable
 */
class MassDisable extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;
    /**
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Webkul\Odoomagentoconnect\Model\Product $model
    ) {
        $this->_filter = $filter;
        $this->_model = $model;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_model->getCollection());
        foreach ($collection as $modelObj) {
            $modelObj->setNeedSync('no');
        }
        $collection->save();
        $this->messageManager->addSuccess(__('Product Need Sync disabled successfully.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
