<?php

class GN_Tools
{
    /**
     * @static
     * @param int $offset
     * @param bool $isDst
     * @return string
     */
    public static function timezoneOffsetToName($offset, $isDst = null)
    {
        if ($isDst === null)
            $isDst = date('I');

        $zone = timezone_name_from_abbr('', $offset, $isDst);
        if ($zone === false) {
            foreach (timezone_abbreviations_list() as $abbr) {
                foreach ($abbr as $city) {
                    if ((bool) $city['dst'] === (bool) $isDst
                    && strlen($city['timezone_id']) > 0
                    && $city['offset'] == $offset
                    ) {
                        $zone = $city['timezone_id'];
                        break;
                    }
                }
                if ($zone !== false)
                    break;
            }
        }

        return $zone;
    }

	const TZ_USER_TO_SERVER = 0;
	const TZ_USER_TO_CUSTOM = 1;
	const TZ_SERVER_TO_USER = 2;
	const TZ_SERVER_TO_CUSTOM = 3;
	const TZ_CUSTOM_TO_USER = 4;
	const TZ_CUSTOM_TO_SERVER = 5;

	public static function switchTimezone($timestamp, $mode, $customTimezone = null) {
		$serverDatetime = new DateTime();
		$serverTimezone = $serverDatetime->getTimezone();
		if (isset($_COOKIE['timezone_offset'])) {
			$timezoneOffset = $_COOKIE['timezone_offset'];
		} else {
			$timezoneOffset = $serverDatetime->getOffset();
		}
		$userTimezone = new DateTimeZone(GN_Tools::timezoneOffsetToName($timezoneOffset));
		switch ($mode) {
			case self::TZ_SERVER_TO_USER:
				$datetime = new DateTime($timestamp, $serverTimezone);
				$datetime->setTimezone($userTimezone);
				break;
			case self::TZ_USER_TO_SERVER:
				$datetime = new DateTime($timestamp, $userTimezone);
				$datetime->setTimezone($serverTimezone);
				break;
			case self::TZ_CUSTOM_TO_USER:
				$datetime = new DateTime($timestamp, new DateTimeZone($customTimezone));
				$datetime->setTimezone($userTimezone);
				break;
			case self::TZ_USER_TO_CUSTOM:
				$datetime = new DateTime($timestamp, $userTimezone);
				$datetime->setTimezone(new DateTimeZone($customTimezone));
				break;
			case self::TZ_CUSTOM_TO_SERVER:
				$datetime = new DateTime($timestamp, new DateTimeZone($customTimezone));
				$datetime->setTimezone($serverTimezone);
				break;
			case self::TZ_SERVER_TO_CUSTOM:
				$datetime = new DateTime($timestamp, $serverTimezone);
				$datetime->setTimezone(new DateTimeZone($customTimezone));
				break;
		}
		return $datetime->format('Y-m-d H:i:s'); //format  'U' zachowuje strefy czasowe, a nam chodzi o ich usunięcie
	}

	/**
     * Tworzy odpowiedni adapter do bazy.
     * Jeśli podany jest $domain, zwraca adapter do odpowiedniej bazy na podstawie domeny.
     * Dla $domain = true zwraca adapter do głównej bazy danych.
     * Dla $domain = null zwraca adapter w zależności od tego czy w requeście jest 'mail' i 'sig' lub gościu jest zalogowany.
     * W innych przypadkach zwracany jest adapter do głównej bazy danych.
     *
     * @param string|bool|null $domain
     * @return Zend_Db_Adapter_Abstract|string
     */
    public static function getAppsDb($domain = null, $onlyDbName = false)
    {
        $options = Zend_Registry::get('application_options');

        if (isset($options['db'])) {
            $db = $options['db'];

            $adapter = $db['adapter'];
            unset($db['adapter']);

            GN_User::init();

            if ($domain !== true && isset($options['googleapps'])) {
                $ga = $options['googleapps'];
                if (isset($ga['singledb']) && $ga['singledb'] == false) {
                    if ($domain === null) {
                        if (isset($_GET['mail']) && isset($_GET['sig'])) {
                            $domain = filter_input(INPUT_GET, 'mail', FILTER_VALIDATE_EMAIL);
                        } else if ($identity = GN_User::getIdentity()) {
                            $domain = $identity->email;
                        } else {
                            // ??? nie wiem co tutaj...
                        }
                    }
                    if ($domain) {
                        if (strpos($domain, '@') !== false)
                            list (, $domain) = explode('@', $domain);

                        $db['dbname'] = GN_User::cleanString($domain);

                        if ($db['prefix'])
                            $db['dbname'] = $db['prefix'] . '_' .  $db['dbname'];
                    }
                }
            }

            unset($db['prefix']);

            $db = Zend_Db::factory('pdo_' . $adapter, $db);
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
        } else {
            $db = Zend_Db_Table::getDefaultAdapter();
        }

        if ($onlyDbName) {
            $db = $db->getConfig();
            return $db['dbname'];
        }

        return $db;
    }
}
