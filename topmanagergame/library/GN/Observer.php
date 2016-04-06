<?php
 
    class GN_Observer
    {
        private $url;
        
        public function __construct($platform_url,$platform_hash,$email,$lang,$src)
        {
            $url=str_replace(strstr($platform_url,'/links/'),'',$platform_url);
            $this->url="$url/observe/index/mail/$email/id/$src/lang/$lang/sig/".GN_User::getSig($email,$platform_hash);
        }
        
        public function observe($action,$result,$data=null)
        {
            $url=$this->url.'/event/'.$action;
            if ($result) $url.='/result/base64:'.base64_encode(serialize($result));
            if (is_null($data)) $data=array_merge($_SERVER,$_REQUEST);
	    else $data=array_merge($data,array('_server'=>$_SERVER));


		//$url.='/debug/1';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST,   1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('data'=>json_encode($data)) );
            $response = curl_exec($ch);
            curl_close($ch);

		//die("<pre>$url\n".print_r($data,1)."\n<hr>$response");
        }
    }
