<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Log extends ActiveRecord\Model
{
    static $table_name = 'logs';
    static $connection = 'admin';
}