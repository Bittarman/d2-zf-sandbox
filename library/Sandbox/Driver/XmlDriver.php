<?php

namespace Sandbox\Driver;

class XmlDriver extends \Doctrine\ORM\Mapping\Driver\XmlDriver
{
    /**
     * Gets the names of all mapped classes known to this driver.
     * 
     * @return array The names of all mapped classes known to this driver.
     */
    public function getAllClassNames()
    {
        $classes = array();
        if ($this->_paths) {
            foreach ((array) $this->_paths as $path) {
                if ( ! is_dir($path)) {
                    throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath();
                }
            
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
        
                foreach ($iterator as $file) {
                    if (($fileName = $file->getBasename($this->_fileExtension)) == $file->getBasename()) {
                        continue;
                    }
                    $className = str_replace('.', '\\', $fileName);
                    // NOTE: All files found here means classes are not transient!
                    if (!in_array($className, $classes)) {
                        $classes[] = $className;
                    }
                }
            }
        }
        return $classes;
    }
    
    protected function _findMappingFile($className)
    {
        $fileName = str_replace('\\', '.', $className) . $this->_fileExtension;
        $mappingFiles = array();
        // Check whether file exists
        foreach ((array) $this->_paths as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $fileName)) {
                 $mappingFiles[] = $path . DIRECTORY_SEPARATOR . $fileName;
            }
        }
        if(empty($mappingFiles)) {
            throw MappingException::mappingFileNotFound($className, $fileName);    
        }
        return $mappingFiles;
    }
    
    protected function _loadMappingFile($file)
    {
        $result = array();
        if (!is_array($file)) {
            $file = (array) $file;   
        }
        $first = each($file);
        array_shift($file);
        $result = parent::_loadMappingFile($first[1]);
        reset($file);
        $className;
        foreach($file as $mappingFile) {
            echo $mappingFile, "\n";
            $mapping = each(parent::_loadMappingFile($mappingFile));
            $className = $mapping['key'];
            $result[$className] = $this->_merge($result[$className], $mapping[1]);
        }
        echo $result[$className];
        return $result;
    }
    
    protected function _merge(\SimpleXMLElement $node1, \SimpleXMLElement $node2) 
    {
        $doc1 = new \DOMDocument();
        $doc2 = new \DOMDocument();
        $doc1->loadXML($node1->asXML());
        $doc2->loadXML($node2->asXML());
        $xpath = new \domXPath($doc2);
        $xpathQuery = $xpath->query('/*/*');
        for ($i = 0; $i < $xpathQuery->length; $i++) {
            $doc1->documentElement->appendChild(
                  $doc1->importNode($xpathQuery->item($i), true));
           }
        $node1 = simplexml_import_dom($doc1);
        return $node1;
    }
}