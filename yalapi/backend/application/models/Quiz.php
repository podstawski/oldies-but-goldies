<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
class Quiz extends AclModel
{
    static $use_view = true;

    static public $validates_presence_of = array(
        array('name'),
        array('time_limit'),
        array('url')
    );

    public function get_time_limit_formatted()
    {
        $minutes = (int)($this->time_limit / 60);
        $seconds = $this->time_limit - 60 * $minutes;
        return str_pad($minutes, 2, 0, STR_PAD_LEFT) . ':' . str_pad($seconds, 2, 0, STR_PAD_LEFT);
    }
}
