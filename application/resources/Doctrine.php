<?php

use \Doctrine\ORM\Mapping\Driver as driver;

class Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var array
     */
    protected $_metaConfig = array();
    
    /**
     * @var \Doctrine\ORM\Mapping\Driver\AbstractFileDriver
     */
    protected $_driver;
    
    /**
     * @var \Doctrine\ORM\Configuration
     */
    protected $_config;
    
    /**
     * @var array
     */
    protected $_connectionOptions;
    
    protected $_entityManager;
    
    public function init()
    {
        $this->_initModules();
        return $this;
    }

    /**
     * Get the Doctrine Configuration object
     *
     * @return \Doctrine\ORM\Configuration
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = new \Doctrine\ORM\Configuration();
            $this->_config->setMetadataDriverImpl($this->getDriver());
        }
        return $this->_config;
    }
    /**
     * Set up metadata driver for modules
     *
     * @return void
     */
    protected function _initModules()
    {
        $bootstrap = $this->getBootstrap();
        $modulePaths = array();
        if ($bootstrap->hasResource('modules')) {
            $bootstrap->bootstrap('modules');
            $modules = $bootstrap->getResource('modules');
            
            foreach($modules as $module) {
                $modulePaths[] = $module->getResourceLoader()->getBasePath() .
                                 $this->_metaConfig['mappingsDir'];
            }
            $this->getDriver()->addPaths($modulePaths);
        }   
    }
    
    /**
     * Get the Doctrine metadata driver
     *
     * @return \Doctrine\ORM\Mapping\Driver\AbstractFileDriver
     */
    public function getDriver()
    {
        if (null === $this->_driver) {
            $defaultPath = APPLICATION_PATH . $this->_metaConfig['mappingsDir'];
            switch ($this->_metaConfig['driver']) {
                case 'php':
                    $driver = new driver\PhpDriver($defaultPath);
                    break;
                case 'xml':
                    $driver = new driver\XmlDriver($defaultPath);
                    break;
                case 'yaml':
                    $driver = new driver\YamlDriver($defaultPath);
                    break;
                default:
                    throw new \RuntimeException('No valid driver found matching ' . $this->_metaConfig['driver']);
            }
            $this->_driver = $driver;
        }
        return $this->_driver;
    }
    
    /**
     * Set up the proxy from configuration
     * 
     * @return void
     */
    public function setProxy($value = null)
    {
        if (!isset($value['dir']) || !is_string($value['dir'])) {
            throw new \RuntimeException('Proxy Directory not specified or invalid');
        }
        if (!isset($value['namespace']) || !is_string($value['namespace'])) {
            throw new \RuntimeException('Proxy Namespace not specified or invalid');
        }
        $config = $this->getConfig();
        $config->setProxyDir($value['dir']);
        $config->setProxyNamespace($value['namespace']);
    }
    
    /**
     * Set up the metadata configuration
     * 
     * @return void
     */
    public function setMetadata($value = null)
    {
        if (!isset($value['driver']) || !is_string($value['driver'])) {
            throw new \RuntimeException('No Valid Doctrine2 Metadata driver specified');
        }
        if (!isset($value['mappingsDir']) || !is_string($value['mappingsDir'])) {
            throw new \RuntimeException('Doctrine2 Metadata directory not set');
        }
        $this->_metaConfig = $value;
    }
    
    public function setConnection($value)
    {
        if (!isset($value['driver']) || !is_string($value['driver'])) {
            throw new \RuntimeException('No valid Doctrine Connection Driver Specified');
        }
        $this->_connectionOptions = $value;
    }
    
    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            $em = \Doctrine\ORM\EntityManager::create($this->_connectionOptions, $this->getConfig());
            $this->_entityManager = $em;
        }
        return $this->_entityManager;
    }
}