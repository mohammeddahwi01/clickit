<?php

/**
 * Product:       Xtento_GridActions (2.1.8)
 * ID:            cVYkdZJQwTYqkWGsQpD22iLUm0k37HB1Jolbds89eUo=
 * Packaged:      2018-08-14T19:26:14+00:00
 * Last Modified: 2017-01-26T13:06:02+00:00
 * File:          app/code/Xtento/GridActions/Ui/Component/MassAction/CustomFilter.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xtento\GridActions\Ui\Component\MassAction;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Filter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomFilter extends \Magento\Ui\Component\MassAction\Filter
{
    /**
     * @param AbstractDb $collection
     * @return AbstractDb
     * @throws LocalizedException
     */
    public function getCollection(AbstractDb $collection)
    {
        if (method_exists($collection, 'setOrderFilter')) {
            $collection->setOrderFilter(['in' => explode(",", $this->request->getParam('order_ids'))]);
        } else {
            $collection->addFieldToFilter('entity_id', ['in' => explode(",", $this->request->getParam('order_ids'))]);
        }
        return $collection;
    }
}
