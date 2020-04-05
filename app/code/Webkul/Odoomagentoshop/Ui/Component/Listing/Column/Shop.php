<?php
/**
 * Webkul Odoomagentoshop Shop Listing Component
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoshop\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Shop extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'column.price';

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_userFactory;
    protected $_shopFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Odoomagentoshop\Model\ShopFactory $shopFactory,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Model\Group $groupObject,
        array $components = [],
        array $data = []
    ) {
        $this->_groupObject = $groupObject;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_shopFactory=$shopFactory;
        $this->_userFactory=$userFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        // if (isset($dataSource['data']['items'])) {
        //     $fieldName = $this->getData('name');
        //     foreach ($dataSource['data']['items'] as & $item) {
        //         // $shop=$this->_shopFactory->create()->load($item['magento_id']);
        //         $user = $this->_groupObject->load($item['magento_id']);
        //         if ($user->getId() == $item['magento_id']) {
        //             $group_name = $user->getCustomerGroupCode();
        //             $item['group_name'] = $group_name;
        //         }
        //     }
        // }

        return $dataSource;
    }
}
