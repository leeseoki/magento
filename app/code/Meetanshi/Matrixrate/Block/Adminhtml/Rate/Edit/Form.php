<?php

namespace Meetanshi\Matrixrate\Block\Adminhtml\Rate\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Meetanshi\Matrixrate\Helper\Data;

/**
 * Class Form
 * @package Meetanshi\Matrixrate\Block\Adminhtml\Rate\Edit
 */
class Form extends WidgetForm
{
    /**
     * @var FormFactory
     */
    protected $formFactory;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var
     */
    protected $backendSession;

    /**
     * Form constructor.
     * @param FormFactory $formFactory
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     */
    public function __construct(FormFactory $formFactory, Context $context, Registry $registry, Data $helper)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return WidgetForm
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]), 'method' => 'post']]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $helper = $this->helper;
        $model = $this->registry->registry('matrixrate_rate');

        $fldDestination = $form->addFieldset('destination', ['legend' => __('Destination')]);

        $fldDestination->addField('method_id', 'hidden', [
            'name' => 'method_id',
        ]);

        $fldDestination->addField('country', 'select', [
            'label' => __('Country'),
            'name' => 'country',
            'options' => $helper->getCountries(),
        ]);

        $fldDestination->addField('state', 'select', [
            'label' => __('State'),
            'name' => 'state',
            'options' => $helper->getStates(),
        ]);

        $fldDestination->addField('city', 'text', [
            'label' => __('City'),
            'name' => 'city',
        ]);

        $fldDestination->addField('zip_from', 'text', [
            'label' => __('Zip From'),
            'name' => 'zip_from',
        ]);

        $fldDestination->addField('zip_to', 'text', [
            'label' => __('Zip To'),
            'name' => 'zip_to',
        ]);

        $fldTotals = $form->addFieldset('conditions', ['legend' => __('Conditions')]);
        $fldTotals->addField('weight_from', 'text', [
            'label' => __('Weight From'),
            'name' => 'weight_from',
        ]);
        $fldTotals->addField('weight_to', 'text', [
            'label' => __('Weight To'),
            'name' => 'weight_to',
        ]);

        $fldTotals->addField('qty_from', 'text', [
            'label' => __('Qty From'),
            'name' => 'qty_from',
        ]);
        $fldTotals->addField('qty_to', 'text', [
            'label' => __('Qty To'),
            'name' => 'qty_to',
        ]);

        $fldTotals->addField('shipping_type', 'select', [
            'label' => __('Shipping Type'),
            'name' => 'shipping_type',
            'options' => $helper->getShippingType(),
        ]);

        $fldTotals->addField('price_from', 'text', [
            'label' => __('Price From'),
            'name' => 'price_from',
            'note' => __('Original product cart price, without discounts.'),
        ]);

        $fldTotals->addField('price_to', 'text', [
            'label' => __('Price To'),
            'name' => 'price_to',
            'note' => __('Original product cart price, without discounts.'),
        ]);

        $fldRate = $form->addFieldset('rate', ['legend' => __('Rate')]);

        $fldRate->addField('cost_base', 'text', [
            'label' => __('Base Rate for the Order'),
            'name' => 'cost_base',
        ]);

        $fldRate->addField('cost_percent', 'text', [
            'label' => __('Percentage per Product'),
            'name' => 'cost_percent',
        ]);

        $fldRate->addField('cost_product', 'text', [
            'label' => __('Fixed Rate per Product'),
            'name' => 'cost_product',
        ]);

        $fldRate->addField('cost_weight', 'text', [
            'label' => __('Fixed Rate per 1 unit of weight'),
            'name' => 'cost_weight',
        ]);

        $fldTotals->addField('time_delivery', 'text', [
            'label' => __('Estimated Delivery (days)'),
            'name' => 'time_delivery',
        ]);

        $data = $this->_backendSession->getFormData(true);
        if ($data) {
            $form->setValues($data);
            $this->_backendSession->setFormData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
}
