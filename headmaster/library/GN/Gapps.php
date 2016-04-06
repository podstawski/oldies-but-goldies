<?php

class GN_Gapps
{

    public static function remoteLogout($sess_id)
    {
        $path = session_save_path() . '/sess_' . $sess_id;
        @unlink($path);
    }

    public static function logout($redirect = '', $email = '', $platform_link = '', $platform_hash = '', $google_logout = '')
    {
        if ($email && $platform_link && $platform_hash) {
            $sig = GN_User::getSig($email, $platform_hash);

            $purl = str_replace('/all/', '/logout/', $platform_link);
            $purl = str_replace('{email}', $email, $purl);
            $purl = str_replace('{sig}', $sig, $purl);
            $purl = sprintf($purl, $email, $sig);

            file_get_contents($purl);
        }

        if ($google_logout) {
            die('<iframe style="display:none" src="https://www.google.com/accounts/Logout"' . ($redirect ? ' onload="location.href=\'' . $redirect . '\';"' : '') . '></iframe>');
        }

        if ($redirect) {
            header('Location: '  . $redirect);
            exit;
        }
    }

    public static function login($logout_url = '', $email = '', $platform_link = '', $platform_hash = '')
    {
        if ($logout_url && $platform_link && $platform_hash && $email) {
            $sig = GN_User::getSig($email, $platform_hash);

            $url = sprintf($platform_link, urlencode($email), $sig);
            $url = str_replace('/all/', '/login/', $url) . '/?logout_url=base64:' . urlencode(base64_encode($logout_url));
            $url = str_replace('{email}', $email, $url);
            $url = str_replace('{sig}', $sig, $url);

            file_get_contents($url);
        }
    }


    public static function oAuthGetAccessToken($scope = 'https://docs.google.com/feeds https://spreadsheets.google.com/feeds', $key = '', $secret = '')
    {
        if (!strlen($key)) $key = 'anonymous';
        if (!strlen($secret)) $secret = 'anonymous';

        $oauthOptions = array(
            'consumerKey' => $key,
            'consumerSecret' => $secret,
            'signatureMethod' => 'HMAC-SHA1',
            'callbackUrl' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
            'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
            'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
        );

        $consumer = new Zend_Oauth_Consumer($oauthOptions);
        if (!empty($_GET) && isset($_COOKIE['req_token'])) {
            $token = $consumer->getAccessToken($_GET, unserialize(base64_decode($_COOKIE['req_token'])));
            self::setCookie('req_token', '');
            return base64_encode(serialize($token));
        }
        else {
            self::setCookie('req_token', base64_encode(serialize($consumer->getRequestToken(array('scope' => $scope)))));
            $consumer->redirect();
        }
    }


    public static function setCookie($key, $val)
    {
        if (headers_sent()) {
            echo "<script>document.cookie='$key=$val'</script>";
        }
        else {
            SetCookie($key, $val);
        }
    }


    public static function getClientFromToken($token, $key = '', $secret = '')
    {

        if (!strlen($key)) $key = 'anonymous';
        if (!strlen($secret)) $secret = 'anonymous';

        if (!is_object($token)) $token = unserialize(base64_decode($token));

        $oauthOptions = array(
            'consumerKey' => $key,
            'consumerSecret' => $secret
        );

        return $token->getHttpClient($oauthOptions);
    }


    public static function createSpreadsheet($_client, $name)
    {
        $client = clone($_client);

        $client->setUri("https://docs.google.com/feeds/default/private/full");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <entry xmlns="http://www.w3.org/2005/Atom" xmlns:docs="http://schemas.google.com/docs/2007">
                    <category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/docs/2007#spreadsheet"/>
                    <title>' . $name . '</title>
                </entry>';

        $client->setMethod('POST');
        $client->setHeaders('GData-Version', '3.0');
        $client->setHeaders('Content-Type', 'application/atom+xml');
        $client->setHeaders('X-Upload-Content-Length', '0');
        $client->setHeaders('Content-Length', strlen($xml));


        $client->setRawData($xml);


        $resp = simplexml_load_string($client->request()->getBody());


        if (!isset($resp->link)) return;


        foreach ($resp->link AS $link) {
            if ($link->attributes()->rel == 'alternate') {

                $l = $link->attributes()->href;
                $id = self::getSpreadsheetId($client, $l, false);
                //die("$l = $id");
                return $id;
            }
        }


        return (string)$resp->id;
    }

    public static function createWorksheet($_client, $ss_id, $title, $rows = 50, $cols = 10)
    {
        $client = clone($_client);


        $client->setUri("https://spreadsheets.google.com/feeds/worksheets/$ss_id/private/full");


        $client->setMethod('POST');
        $client->setHeaders('GData-Version', '3.0');
        $client->setHeaders('Content-Type', 'application/atom+xml');


        $xml = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                <title>' . $title . '</title>
                <gs:rowCount>' . $rows . '</gs:rowCount>
                <gs:colCount>' . $cols . '</gs:colCount>
            </entry>';
        $client->setHeaders('Content-Length', strlen($xml));

        $client->setRawData($xml);

        $resp = simplexml_load_string($client->request()->getBody());

        $id = array_pop(explode("/", (string)$resp->id));
        return $id;
        return self::getWorksheetId($client, $ss_id, $title, false);
    }

    public static function deleteWorksheet($_client, $ss_id, $title)
    {
        $client = clone($_client);

        $ws_id = self::getWorksheetId($client, $ss_id, $title, false);
        if (!$ws_id) return;

        $uri = "https://spreadsheets.google.com/feeds/worksheets/$ss_id/private/full/$ws_id/version?delete=true";
        echo "$uri\n";
        $client->setUri($uri);
        $client->setMethod('DELETE');
        $client->setHeaders('GData-Version', '3.0');
        $client->setHeaders('Content-Type', 'application/atom+xml');
        $resp = $client->request()->getBody();

        //print_r($resp."\n");
    }

    public static function getWorksheetIds($client, $ss_id)
    {
        $res = array();

        $c = new Zend_Gdata_Spreadsheets($client);
        $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
        $query->setSpreadsheetKey($ss_id);
        $feed = $c->getWorksheetFeed($query);
        foreach ($feed->entries as $entry) {

            $res[array_pop(explode("/", $entry->id->text))] = $entry->title->text;

        }

        return $res;
    }


    public static function getWorksheetId($client, $ss_id, $title, $createIfNotFound = true, $rows = 50, $cols = 10)
    {
        $c = new Zend_Gdata_Spreadsheets($client);
        $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
        $query->setSpreadsheetKey($ss_id);
        $feed = $c->getWorksheetFeed($query);
        foreach ($feed->entries as $entry) {
            if ($entry->title->text == $title) {
                //print_r($entry);
                return array_pop(explode("/", $entry->id->text));
            }
        }

        if ($createIfNotFound) return self::createWorksheet($client, $ss_id, $title, $rows, $cols);


    }


    public static function getSpreadsheetId($client, $nameORlink, $createIfNotFound = true)
    {
        $link = substr($nameORlink, 0, 8) == 'https://';

        $c = new Zend_Gdata_Spreadsheets($client);
        $feed = $c->getSpreadsheetFeed();


        foreach ($feed->entries as $entry) {
            if (!$link && $entry->title->text == $nameORlink) {
                return array_pop(explode("/", $entry->id->text));
            }

            if ($link) {
                foreach ($entry->link AS $l) {
                    if ($l->rel == 'alternate') return array_pop(explode("/", $entry->id->text));
                }
            }
        }


        if ($createIfNotFound && !$link) {
            return self::createSpreadsheet($client, $nameORlink);
        }

    }


    public static function dbArray2Spreadsheet($client, $array, $spreadsheetName, $ss_id = null, $createWorksheetsOnly = false, $worksheetName = 'Sheet 1')
    {
        //$client=clone($_client);
        if (is_null($ss_id)) $ss_id = self::getSpreadsheetId($client, $spreadsheetName);


        if (!$ss_id) return false;

        $a = array();
        foreach (array_keys($array) AS $key) {
            if (is_numeric($key)) $a[] = $array[$key];
            else self::dbArray2Spreadsheet($client, $array[$key], $spreadsheetName, $ss_id, $createWorksheetsOnly, $key);
        }
        if (!count($a)) return;

        $rows = count($a) + 5;
        $cols = count($a[0]) + 5;

        $ws_id = self::getWorksheetId($client, $ss_id, $worksheetName, true, $rows, $cols);

        if ($createWorksheetsOnly) return;

        $x = 1;

        //$client2=clone($_client);
        //$client2->setHeaders('If-Match: *');
        $c = new Zend_Gdata_Spreadsheets($client);


        foreach ($a AS $row) {
            if ($x == 1) {
                $y = 1;
                foreach (array_keys($row) AS $k) $c->updateCell($x, $y++, $k, $ss_id, $ws_id);
                $x = 2;
            }

            $y = 1;
            foreach ($row AS $k) $c->updateCell($x, $y++, $k, $ss_id, $ws_id);
            $x++;
        }

        return true;
    }

    public static function getSpreadsheetList($client)
    {
        $c = new Zend_Gdata_Spreadsheets($client);
        $feed = $c->getSpreadsheetFeed();

        $res = array();
        foreach ($feed->entries AS $entry) {

            foreach ($entry->link AS $l) {
                if ($l->rel == 'alternate') $id = array_pop(explode("/", $entry->id->text));
            }

            $res[$id] = (String)$entry->title;
        }

        return $res;
    }

    public static function getWorksheet($client, $spreadsheetId, $worksheetId = '')
    {
        $c = new Zend_Gdata_Spreadsheets($client);
        $query = new Zend_Gdata_Spreadsheets_ListQuery();
        $query->setSpreadsheetKey($spreadsheetId);
        if ($worksheetId) $query->setWorksheetId($worksheetId);

        $listFeed = $c->getListFeed($query);
        $res = array();
        foreach ($listFeed->entries AS $entry) {
            $rowData = $entry->getCustom();
            $row = array();
            foreach ($rowData as $customEntry) {
                $row[$customEntry->getColumnName()] = $customEntry->getText();
            }
            $res[] = $row;
        }

        return $res;
    }

    public static function search($client, $q, $order = 'DCM')
    {

        $result = array();
        for ($i = 0; $i < strlen($order); $i++) {
            switch ($what = $order[$i]) {

                case 'M':
                    //kurwa nie ma API
                    break;


                case 'D':
                    $c = new Zend_Gdata_Docs($client);
                    $query = new Zend_Gdata_Docs_Query();
                    $query->setQuery($q);
                    $feed = $c->getDocumentListFeed($query);

                    foreach ($feed->entries as $entry) {
                        $r = array('type' => $what, 'name' => $entry->title->text);
                        foreach ($entry->link AS $l) {
                            if ($l->rel == 'alternate') $r['link'] = $l->href;
                        }

                        $result[] = $r;

                    }

                    break;

                case 'C':
                    $c = new Zend_Gdata_Calendar($client);
                    $query = $c->newEventQuery();
                    //$query = new Zend_Gdata_Calendar_EventQuery();
                    $query->setUser('default');
                    $query->setVisibility('private');
                    $query->setProjection('full');
                    $query->setOrderby('starttime');
                    $query->setFutureEvents('true');
                    $query->setSingleEvents('true');
                    $query->setQuery($q);

                    $feed = $c->getCalendarEventFeed($query);

                    foreach ($feed AS $event) {
                        $r = array('type' => $what, 'name' => $event->title->text, 'start' => $event->when[0]->startTime);

                        foreach ($event->link AS $l) {
                            if ($l->rel == 'alternate') $r['link'] = $l->href;
                        }

                        $result[] = $r;
                    }
                    break;


            }
        }


        return $result;
    }

}
