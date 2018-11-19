<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Controller\Adminhtml\Archive;

class MassRemovePermanently extends \Amasty\Orderarchive\Controller\Adminhtml\Action
{
    /**
     * @param array $selectedIds
     * @return void
     */
    protected function massAction($selectedIds)
    {
        $result = $this->orderProcessor->removePermanently($selectedIds);
        $this->messageManager->addSuccessMessage(
            $this->helper->getInformationString($result, self::REMOVE_PERMANENTLY_METHOD_CODE)
        );
    }
}
