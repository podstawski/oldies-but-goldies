<?php

class GN_Observer
{
    private $url;

    public function __construct($platform_url, $platform_hash, $email, $lang, $src)
    {
        if (is_array($platform_url)) {
            $domain = $this->extract_domain_special($email);
            $platform_url = isset($platform_url[$domain]) ? $platform_url[$domain] : $platform_url[0];
        }

        if (is_array($platform_hash)) {
            $domain = $this->extract_domain_special($email);
            $platform_hash = isset($platform_hash[$domain]) ? $platform_hash[$domain] : $platform_hash[0];
        }

        //$url=str_replace(strstr($platform_url,'/links/'),'',$platform_url);

        $url = str_replace('/links/all', '/observe/index', $platform_url);

        $pos = strpos($url, 'mail/') ? : strpos($url, 'mail=');
        $sig = GN_User::getSig($email, $platform_hash);
        if ($pos) {
            $m = substr($url, $pos + 5);
            $m = substr($m, 0, strpos($m, '&') ? : strpos($m, '/'));
            if (strstr($m, '@'))
                $sig = GN_User::getSig($m, $platform_hash);
        }

        $url = str_replace('mail/%s', 'mail/' . $email, $url);
        $url = str_replace('sig/%s', 'sig/' . $sig, $url);
        $url = str_replace('mail=%s', 'mail=' . $email, $url);
        $url = str_replace('sig=%s', 'sig=' . $sig, $url);

        $url = str_replace('{email}', $email, $url);
        $url = str_replace('{sig}', $sig, $url);

        if (!$lang)
            $lang = 'en';

        if (strstr($url, '?')) {
            $url .= '&lang=' . $lang;
        } else {
            $url .= '/lang/' . $lang;
        }

        $this->url = $url;
    }

    private function extract_domain_special($email)
    {
        $e = explode('@', $email);
        return str_replace('.', '_', $e[1]);
    }


    public function observe($action, $result, $data = null, $lang = null)
    {
        if (strstr($this->url, '?')) {
            $url = $this->url . '&event=' . $action;
            if ($result)
                $url .= '&result=base64:' . base64_encode(serialize($result));
        } else {
            $url = $this->url . '/event/' . $action;
            if ($result)
                $url .= '/result/base64:' . base64_encode(serialize($result));
        }

        if ($lang) {
            $url = preg_replace('#/lang/[a-z][a-z]#', '/lang/' . $lang, $url);
            $url = preg_replace('#&lang=[a-z][a-z]#', '&lang=' . $lang, $url);
        }

        if (is_null($data))
            $data = array_merge($_SERVER, $_REQUEST);
        else
            $data = array_merge($data, array('_server' => $_SERVER));

        //$url.='/debug/1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => json_encode($data)));
        $response = curl_exec($ch);
        curl_close($ch);

        $ret = json_decode($response);
        //die("<pre>$url\n".print_r($data,1)."\n<hr>".print_r($ret,1));
        //file_put_contents('/tmp/esa-'.rand(1000,9000),"$url\n".print_r($data,1)."\n".print_r($ret,1));

        return $ret;
    }
}
