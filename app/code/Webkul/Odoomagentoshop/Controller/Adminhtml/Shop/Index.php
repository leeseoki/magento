<?php
/**
 * Webkul Odoomagentoshop Shop Index Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoshop\Controller\Adminhtml\Shop;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Webkul_Odoomagentoshop::manager';

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_Odoomagentoshop::manager');
        $resultPage->addBreadcrumb(__('Odoomagentoshop Shop'), __('Odoomagentoshop Shop'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shop(s) Mapping'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoshop::shop_list');
    }
}
