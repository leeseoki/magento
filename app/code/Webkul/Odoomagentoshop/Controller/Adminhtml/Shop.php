<?php
/**
 * Webkul Odoomagentoshop Shop Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoshop\Controller\Adminhtml;

abstract class Shop extends \Magento\Backend\App\AbstractAction
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * Shop model factory
     *
     * @var \Webkul\Odoomagentoshop\Model\shopFactory
     */
    protected $_shopFactory;

    protected $_scopeConfig;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Webkul\Odoomagentoshop\Model\shopFactory $shopFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Odoomagentoshop\Model\ShopFactory $shopFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomagentoshop\Model\Shop $shopMapping
    ) {
    
        parent::__construct($context);
        $this->_shopMapping = $shopMapping;
        $this->_coreRegistry = $coreRegistry;
        $this->_userFactory = $userFactory;
        $this->_shopFactory = $shopFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Webkul_Odoomagentoshop::manager'
        );
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_User::acl_users');
    }
}
