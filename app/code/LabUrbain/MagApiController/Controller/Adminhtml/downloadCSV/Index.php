<?php
namespace LabUrbain\MagApiController\Controller\Adminhtml\downloadCSV;

class Index extends \Magento\Backend\App\Action
{
  /**
  * Index Action*
  * @return void
  */
  public function execute()
  {
	  require_once("buildCSV.php");
  }
}
