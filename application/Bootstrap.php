<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initAppResources()
    {
        $loader = $this->getResourceLoader();
        $loader->addResourceType('resource', 'resources/', 'Resource');
    }
}

