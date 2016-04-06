<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

require_once 'GN/ShellPrinter.php';
$shellPrinter = new ShellPrinter();

Yala_User::setIdentity('yala');

$domains = App::all();
$shellPrinter->star('Found ' . count($domains) . ' domains');
foreach ($domains as $row)
{
    Yala_User::init(null, $row->domain);
    Yala_User::setIdentity('admin');
    $shellPrinter->info('Updating domain ' . $row->domain);
    UserProfile::update_all(array(
        'set' => array(
            'tax_identification_number' => null
        )
    ));
}

Yala_User::getInstance()->clearIdentity();