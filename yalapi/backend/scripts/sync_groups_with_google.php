<?php

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
//$shellPrinter->demo();

$databases = array();
if (isset($argv[1])) {
    $domain = $argv[1];
    $databases[$domain] = GN_User::cleanString($domain);
} else {
    Yala_User::init();
    Yala_User::setIdentity('yala');

    foreach (App::all() as $appRow) {
        $databases[$appRow->domain] = GN_User::cleanString($appRow->domain);
    }

    $shellPrinter->info('Found ' . count($databases) . ' domains');
}

foreach ($databases as $domain => $dbname)
{
    $shellPrinter->eol();
    $shellPrinter->info('Syncing groups for domain: ' . $domain);

    Yala_User::init(null, $domain);
    Yala_User::setIdentity('admin');

    if ($accessToken = App::get_access_token_by_domain($domain))
    {
        $gappsClient = new Zend_Gdata_Gapps(
            $accessToken->getHttpClient(Zend_Registry::get('oauth_options')),
            $domain
        );

        foreach (Group::all(array('order' => 'name ASC')) as $k => $dbGroup)
        {
            if ($k) {
                $shellPrinter->eol();
            }
            $shellPrinter->star('Syncing group "' . $dbGroup->name . '"');

            if (empty($dbGroup->google_group_id)) {
                $dbGroup->google_group_id = Group::find_google_id($dbGroup);
                $dbGroup->save();
            }

            $groupID = $dbGroup->google_group_id;

            if ($gappsClient->retrieveGroup($groupID) == null) {
                $shellPrinter->plus('Adding group "' . $dbGroup->name . '"');
                $gappsClient->createGroup($groupID, $dbGroup->name);
		        sleep(1);
            } else {
                $shellPrinter->star('Updating group name');
                $gappsClient->updateGroup($groupID, $dbGroup->name);
            }

            $shellPrinter->info('Collecting google group members', false);
            $googleGroupMembers = array();
            foreach ($gappsClient->retrieveAllMembers($groupID)->getEntry() as $memberEntry) {
                $memberID = $memberEntry->property[1]->value;
                $googleGroupMembers[$memberID] = $memberEntry;
            }
            $shellPrinter->append('found ' . $shellPrinter::GREEN . count($googleGroupMembers));

            $shellPrinter->info('Collecting db group members', false);
            $dbGroupMembers   = array();
            $nonGoogleMembers = array();
            foreach ($dbGroup->users as $memberEntry) {
                if (empty($memberEntry->email)) {
                    $nonGoogleMembers[] = $memberEntry;
                } else {
                    $dbGroupMembers[$memberEntry->email] = $memberEntry;
                }
            }
            $shellPrinter->append('found ' . $shellPrinter::GREEN . count($dbGroupMembers), $shellPrinter::APPEND);

            foreach ($nonGoogleMembers as $memberEntry) {
                $shellPrinter->star_white('User ' . $memberEntry->username . ' is non google member, skipping');
            }

            foreach ($dbGroupMembers as $memberID => $memberEntry) {
                if (array_key_exists($memberID, $googleGroupMembers) == false) {
                    $shellPrinter->plus('Adding ' . $memberID);
                    $gappsClient->addMemberToGroup($memberID, $groupID);
                }
            }

            foreach ($googleGroupMembers as $memberID => $memberEntry) {
                if (array_key_exists($memberID, $dbGroupMembers) == false) {
                    $shellPrinter->minus('Removing ' . $memberID);
                    $gappsClient->removeMemberFromGroup($memberID, $groupID);
                }
            }
        }
    } else {
        $shellPrinter->warn('Could not find access token');
    }
}

$shellPrinter->eol();
$shellPrinter->info('Groups synced!');
Yala_User::getInstance()->clearIdentity();