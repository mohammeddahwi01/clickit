<?php 
echo 5;
$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

$query = 'show tables' ;
$rows = $readConnection->fetchAll($query); 
print_r($rows);
echo 5;

?>