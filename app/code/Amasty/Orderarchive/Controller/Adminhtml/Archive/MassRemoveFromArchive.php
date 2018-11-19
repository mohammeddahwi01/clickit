<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Controller\Adminhtml\Archive;

class MassRemoveFromArchive extends \Amasty\Orderarchive\Controller\Adminhtml\Action
{
    /**
     * @param array $selectedIds
     * @return void
     */
    protected function massAction($selectedIds)
    {
        $result = $this->orderProcessor->removeFromArchive($selectedIds);
        $this->messageManager->addSuccessMessage(
            $this->helper->getInformationString($result, self::REMOVE_FROM_ARCHIVE_METHOD_CODE)
        );
    }
}
