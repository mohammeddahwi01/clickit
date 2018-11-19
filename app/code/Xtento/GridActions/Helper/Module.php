<?php

/**
 * Product:       Xtento_GridActions (2.1.8)
 * ID:            cVYkdZJQwTYqkWGsQpD22iLUm0k37HB1Jolbds89eUo=
 * Packaged:      2018-08-14T19:26:14+00:00
 * Last Modified: 2016-05-30T13:09:27+00:00
 * File:          app/code/Xtento/GridActions/Helper/Module.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\GridActions\Helper;

class Module extends \Xtento\XtCore\Helper\AbstractModule
{
    protected $edition = 'CE';
    protected $module = 'Xtento_GridActions';
    protected $extId = 'MTWOXtento_Spob152689';
    protected $configPath = 'gridactions/general/';

    // Module specific functionality below

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return parent::isModuleEnabled();
    }
}
