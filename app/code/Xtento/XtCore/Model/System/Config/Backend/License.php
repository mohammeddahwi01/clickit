<?php

/**
 * Product:       Xtento_XtCore (2.3.0)
 * ID:            cVYkdZJQwTYqkWGsQpD22iLUm0k37HB1Jolbds89eUo=
 * Packaged:      2018-08-14T19:26:14+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Model/System/Config/Backend/License.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\System\Config\Backend;

class License extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $this->_registry->register('xtento_configuration_license_key', $this->getValue(), true);
    }
}
