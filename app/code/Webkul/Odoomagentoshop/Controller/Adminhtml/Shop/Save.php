<?php
/**
 * Webkul Odoomagentoshop Shop Save Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoshop\Controller\Adminhtml\Shop;

use Magento\Framework\Exception\AuthenticationException;

class Save extends \Webkul\Odoomagentoshop\Controller\Adminhtml\Shop
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('odoomagentoshop/*/');
            return;
        }

        /** Before updating admin user data, ensure that password of current admin user is entered and is correct */
        try {
            $this->messageManager->addSuccess(__('You saved the shop.'));
            
            $shopId = (int)$this->getRequest()->getParam('entity_id');
            $shopmodel = $this->_shopMapping;
            if ($shopId) {
                $shopmodel->load($shopId);
                $shopmodel->setId($shopmodel);
                $data['id']=$shopId;
            }
            if ($shopId && $shopmodel->isObjectNew()) {
                $this->messageManager->addError(__('This shop no longer exists.'));
                $this->_redirect('odoomagentoshop/*/');
                return;
            }
            $data['created_by'] = 'Manual Mapping';
            $shopmodel->setData($data);
            $shopmodel->save();
            $this->_getSession()->setUserData(false);
            $this->_redirect('odoomagentoshop/*/');
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($data);
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoshop::shop_save');
    }

    /**
     * @param \Magento\User\Model\User $model
     * @param array $data
     * @return void
     */
    protected function redirectToEdit(array $data)
    {
        $this->_getSession()->setUserData($data);
        $data['entity_id']=isset($data['entity_id'])?$data['entity_id']:0;
        $arguments = $data['entity_id'] ? ['id' => $data['entity_id']]: [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        if ($data['entity_id']) {
            $this->_redirect('odoomagentoshop/*/edit', $arguments);
        } else {
            $this->_redirect('odoomagentoshop/*/index', $arguments);
        }
    }
}
