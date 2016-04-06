<?php
/**
 * @author <piotr.podstawski@gammanet.pl> Piotr Podstawski
 */

class GN_GClient_NBSocket extends Zend_Http_Client_Adapter_Socket
{
    private $_waiting_for_response = false;

    private $_debug_file = null;

    private function _debug($txt)
    {
        if (is_null($this->_debug_file)) $this->_debug_file = '/tmp/nb-socket-'.md5(rand(1,9999999));


        fwrite($f=fopen($this->_debug_file,'a'),$txt."\n-----------------------------\n");
        fclose($f);
    }

    public function connect($host, $port = 80, $secure = false)
    {
        if ($this->_waiting_for_response) return;

        parent::connect($host,$port,$secure);
        if ($this->socket) stream_set_blocking($this->socket,0);
    }

    public function read()
    {
        $this->_waiting_for_response = true;
        $read=parent::read();
        $this->_waiting_for_response = !feof($this->socket);

        if (strlen($read) && $this->_waiting_for_response)
        {
            while ($this->_waiting_for_response=!feof($this->socket))
            {
                $read.=@fgets($this->socket);
                $this->_checkSocketReadTimeout();
                if (!$this->socket) break;
            }
        }

        //$this->_debug((feof($this->socket)?'KONIEC':'JESZCZE')."\n".$read);

        return $read;
    }

    public function close()
    {
        if ($this->_waiting_for_response) return;
        parent::close();
    }

    public function write($method, $uri, $http_ver = '1.1', $headers = array(), $body = '')
    {
        if ($this->_waiting_for_response) return '';
        return parent::write($method, $uri, $http_ver, $headers, $body );
    }


    protected function _checkSocketReadTimeout()
    {
        $waiting_for_response=$this->_waiting_for_response;
        $this->_waiting_for_response=false;
        parent::_checkSocketReadTimeout();

        $this->_waiting_for_response=$waiting_for_response;
    }


}