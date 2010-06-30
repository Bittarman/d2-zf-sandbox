<?php
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));
die(APPLICATION_PATH);
if (file_exists($envfile = __DIR__ . '/env.php'));
require_once ($envfile);
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once('Zend/Application.php');
$application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );
$dc = $application->bootstrap()->getResource('Doctrine');
Zend_Debug::dump($dc);die();
$helperSet = new \Symfony\Components\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
