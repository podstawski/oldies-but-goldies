<?php

class RestClient
{
    const REST_COOKIE_NAME = 'rest_client_id';

    private $_baseUrl;
    private $_userData;
    private $_cookie_file;

    public function __construct($baseUrl, array $userData = null)
    {
        $this->_baseUrl = $baseUrl;
        $this->_userData = $userData;

        if (!isset($_COOKIE[self::REST_COOKIE_NAME])) {
            $cookie_name = md5(time() . rand(100000, 999999));
            $this->_setCookie(self::REST_COOKIE_NAME, $cookie_name);
            $_COOKIE[self::REST_COOKIE_NAME] = $cookie_name;
        } else {
            $cookie_name = $_COOKIE[self::REST_COOKIE_NAME];
        }

        $this->_cookie_file = sys_get_temp_dir() . '/' . $cookie_name;
        if (!file_exists($this->_cookie_file)) {
            touch($this->_cookie_file);
        }
    }


    private function _setCookie($k, $v)
    {
        if (headers_sent()) echo "<script>document.cookie='$k=$v';</script>";
        else setCookie($k, $v);
    }

    public function _makeCurlRequest($url, $rest, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_baseUrl . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101124 Gentoo Firefox/3.6.12');

        if ($data) {
            //            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($rest));
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookie_file);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function login()
    {
        $response = json_decode($this->_makeCurlRequest('/login?rest=true', 'POST', "username={$this->_userData['username']}&password={$this->_userData['password']}"));
        return ($response !== false && isset($response->id));
    }

    public function logout()
    {
        unlink($this->_cookie_file);
    }

    public function get($url, $method = 'GET')
    {
        return $this->_makeCurlRequest($url, $method);
    }

}