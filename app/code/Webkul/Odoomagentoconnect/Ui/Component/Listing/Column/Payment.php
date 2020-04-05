<?php
/**
 * Webkul Odoomagentoconnect Payment Listing Component
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoconnect\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

class Payment extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'column.price';

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_userFactory;
    protected $_paymentFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Odoomagentoconnect\Model\PaymentFactory $paymentFactory,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
    
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_paymentFactory=$paymentFactory;
        $this->_userFactory=$userFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'odoomagentoconnect/payment/edit',
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
