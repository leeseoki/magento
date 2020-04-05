<?php

namespace Webkul\Odoomagentoshop\Block\Adminhtml;

/**
 * Webkul Odoomagentoshop Shop
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

class Shop extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Magento\User\Model\ResourceModel\User
     */
    protected $_resourceModel;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\User\Model\ResourceModel\User $resourceModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\User\Model\ResourceModel\User $resourceModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addData(
            [
                \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'shop',
                \Magento\Backend\Block\Widget\Grid\Container::PARAM_BLOCK_GROUP => 'Webkul_Odoomagentoshop',
                \Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_NEW => __('Add Store User'),
                \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => __('Odoomagentoshop'),
            ]
        );
        parent::_construct();
        $this->_addNewButton();
    }
}
