<?php
/**
 * Webkul Odoomagentoshop Shop Tab Main Block
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

// @codingStandardsIgnoreFile

namespace Webkul\Odoomagentoshop\Block\Adminhtml\Shop\Edit\Tab;
use Webkul\Odoomagentoconnect\Helper\Connection;
use xmlrpc_client;
use xmlrpcval;
use xmlrpcmsg;
/**
 * Cms page edit form main tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Store\Model\StoreRepository $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_authSession = $authSession;
        $this->_localeLists = $localeLists;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \Magento\User\Model\User */
        $odooShop = $this->getOdooShopArray();
        $magentoStore = $this->getMagentoStoreArray();
        $model = $this->_coreRegistry->registry('odoomagentoshop_user');
        $shopModel = $this->_coreRegistry
                            ->registry('odoomagentoshop_shop');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $baseFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Store Mapping'), 'class' => 'fieldset-wide']
        );

        if ($shopModel->getEntityId()) {
            $baseFieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
        $baseFieldset->addField(
            'magento_id',
            'select',
            [
            'label' => __('Stores'),
            'title' => __('Stores'),
            'name' => 'magento_id',
            'required' => true,
            'options' => $magentoStore]
        );
        $baseFieldset->addField(
            'odoo_id',
            'select',
            [
                    'label' => __('Odoo Shop'),
                    'title' => __('Odoo Shop'),
                    'name' => 'odoo_id',
                    'required' => true,
                    'options' => $odooShop
            ]
        );

        $data= $shopModel->getData();
        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getMagentoStoreArray()
    {

        $storeManagerDataList = $this->_storeManager->getStores();
        $options = array();
        $options[0] ='--Select Magento Store--';
        foreach ($storeManagerDataList as $key => $value) {
            $options[$key] = $value['code'];
        }
        return $options;
    }

    public function getOdooShopArray()
    {
        $selection = [];
        $helper = $this->_objectManager->create('\Webkul\Odoomagentoconnect\Helper\Connection');
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        $errorMessage = $helper->getSession()->getErrorMessage();

        if ($userId > 0) {
            $selection[0] ='--Select Odoo Shop--';
            $context = $helper->getOdooContext();
            $client = $helper->getClientConnect();
            $key = [];
            $key1 = [
                new xmlrpcval('id', 'int'),
                new xmlrpcval('name', 'string')
            ];
            $msgSer = new xmlrpcmsg('execute');
            $msgSer->addParam(new xmlrpcval(Connection::$odooDb, "string"));
            $msgSer->addParam(new xmlrpcval($userId, "int"));
            $msgSer->addParam(new xmlrpcval(Connection::$odooPwd, "string"));
            $msgSer->addParam(new xmlrpcval("sale.shop", "string"));
            $msgSer->addParam(new xmlrpcval("search", "string"));
            $msgSer->addParam(new xmlrpcval($key, "array"));
            $resp0 = $client->send($msgSer);
            $val = $resp0->value()->me['array'];

            $msgSer1 = new xmlrpcmsg('execute');
            $msgSer1->addParam(new xmlrpcval(Connection::$odooDb, "string"));
            $msgSer1->addParam(new xmlrpcval($userId, "int"));
            $msgSer1->addParam(new xmlrpcval(Connection::$odooPwd, "string"));
            $msgSer1->addParam(new xmlrpcval("sale.shop", "string"));
            $msgSer1->addParam(new xmlrpcval("read", "string"));
            $msgSer1->addParam(new xmlrpcval($val, "array"));
            $msgSer1->addParam(new xmlrpcval($key1, "array"));
            $msgSer1->addParam(new xmlrpcval($context, "struct"));
            $resp1 = $client->send($msgSer1);
            $val = $resp1->value()->me['array'];

            if ($resp0->faultCode()) {
                array_push(
                    $selection,
                    [
                        'label' => $helper->__('Not Available(Error in Fetching)'),
                        'value' => ''
                    ]
                );
                return $selection;
            } else {
                $valueArray = $resp1->value()->scalarval();
                foreach ($valueArray as $value) {
                    $id = $value->me['struct']['id']->me['int'];
                    $name = $value->me['struct']['name']->me['string'];
                    $selection[$id] = $name;
                }
            }
            return $selection;
        } else {
            $selection['error'] = $errorMessage;
            return $selection;
        }
    }
}
