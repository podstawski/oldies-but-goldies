<?php

@list (, $domain, $groupName, $roleName, $limit) = $argv;

if (!$domain)    die('please provide domain' . PHP_EOL);
if (!$groupName) die('please provide group name' . PHP_EOL);
if (!$roleName)  die('please provide role name' . PHP_EOL);

//$domain    = 'promienko.pl';
//$groupName = 'group_test';
//$roleName  = 'user';

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

require_once APPLICATION_PATH . '/configs/AclRules.php';
$roleID = AclRules::$role_token_trans[$roleName];
if (!isset($roleID)) die('invalid role name "' . $roleName . '"' . PHP_EOL);

Yala_User::init(null, $domain);
Yala_User::setIdentity('admin');

$accessToken = App::get_access_token_by_domain($domain);
if (!$accessToken) die('could not find access token for domain ' . $domain . PHP_EOL);

$gappsClient = new Zend_Gdata_Gapps(
    $accessToken->getHttpClient(Zend_Registry::get('oauth_options')),
    $domain
);

$groupMembers = $gappsClient->retrieveAllMembers($groupName)->getEntry();
$shellPrinter->info('Found ' . count($groupMembers) . ' users in group "' . $groupName , '"');
if ($limit) {
    $shellPrinter->info('Limiting group members count to ' . $limit);
    $groupMembers = array_slice($groupMembers, 0, $limit);
}
$shellPrinter->eol();
foreach ($groupMembers as $memberEntry) {
    $memberID = $memberEntry->property[1]->value;
    list ($login, $domain) = explode('@', $memberID);
    Yala_User::init(null, $domain);
    Yala_User::setIdentity('admin');
    if ($user = User::find_by_email($memberID)) {
        if ($user->role_id != $roleID) {
            $user->role_id = $roleID;
            @$user->save();
            $shellPrinter->star($memberID . ': role ' . $shellPrinter::PURPULE . 'changed');
        } else {
            $shellPrinter->star_white($memberID . ': role ' . $shellPrinter::GREEN . 'OK' . $shellPrinter::DEFAULT_COLOR . ', skipping');
        }
    } else {
        $shellPrinter->warn($memberID . ': user not found');
    }
}
$shellPrinter->eol();
$shellPrinter->info('Roles updated!');
Yala_User::getInstance()->clearIdentity();