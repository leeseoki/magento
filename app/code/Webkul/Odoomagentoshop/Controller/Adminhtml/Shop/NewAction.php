<?php
/**
 * Webkul Odoomagentoshop Shop NewAction Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoshop\Controller\Adminhtml\Shop;

class NewAction extends \Webkul\Odoomagentoshop\Controller\Adminhtml\Shop
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoshop::shop_new');
    }
}
