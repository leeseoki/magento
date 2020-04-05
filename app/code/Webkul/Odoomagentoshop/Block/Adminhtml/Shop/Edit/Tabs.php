<?php
namespace Webkul\Odoomagentoshop\Block\Adminhtml\Shop\Edit;

/**
 * Webkul Odoomagentoshop Shop Tabs Block
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Shop Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Shop Manual Mapping'),
                'title' => __('Shop Manual Mapping'),
                'content' => $this->getLayout()
                                ->createBlock('Webkul\Odoomagentoshop\Block\Adminhtml\Shop\Edit\Tab\Main')
                                ->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
