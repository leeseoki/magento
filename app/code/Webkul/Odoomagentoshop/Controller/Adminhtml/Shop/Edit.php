<?php
/**
 * Webkul Odoomagentoshop Shop Edit Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoshop\Controller\Adminhtml\Shop;

use Magento\Framework\Locale\Resolver;

class Edit extends \Webkul\Odoomagentoshop\Controller\Adminhtml\Shop
{
    /**
     * @return void
     */
    public function execute()
    {
        $shopId = (int)$this->getRequest()->getParam('id');
        $shopmodel=$this->_shopFactory->create();
        if ($shopId) {
            $shopmodel->load($shopId);
            if (!$shopmodel->getEntityId()) {
                $this->messageManager->addError(__('This Shop no longer exists.'));
                $this->_redirect('odoomagentoshop/*/');
                return;
            }
        }
      
        $userId = $shopmodel->getId();
        /** @var \Magento\User\Model\User $model */
        $model = $this->_userFactory->create();

        $data = $this->_session->getUserData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('shop_user', $model);
        $this->_coreRegistry->register('odoomagentoshop_shop', $shopmodel);

        if (isset($userId)) {
            $breadcrumb = __('Edit Shop');
        } else {
            $breadcrumb = __('New Shop');
        }
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->getPage()->getConfig()
                                ->getTitle()
                                ->prepend(__('Users'));
        $this->_view->getPage()->getConfig()
                                ->getTitle()
                                ->prepend($model->getId() ? $model->getName() : __('Shop Manual Mapping'));
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoshop::shop_view');
    }
}
