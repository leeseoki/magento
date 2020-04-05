<?php
/**
 * Webkul Odoomagentoconnect Logs Clearfile Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Logs;

class Clearfile extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $_resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->directory_list = $directory_list;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::synchronization_logs');
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $dir = $this->directory_list->getPath('log');
        $files = scandir($dir);
        $path = '';
        $writerClass = new \Zend\Log\Writer\Stream(BP . '/var/log/webkul.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writerClass);

        $logger->info($files);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if ($file == "system.log") {
                $path = $dir.'/'.$file ;
                $fh = fopen($path, 'r+');
                if ($fh === false) {
                    $err = error_get_last();
                    if (!empty($err)) {
                        throw new Exception($this->_helper->__('Error emptying file: %s', $err['message']));
                    }

                    throw new Exception($this->_helper->__('Error emptying file.'));
                }
                ftruncate($fh, 0);
                fclose($fh);      
            }
        }
    }
}