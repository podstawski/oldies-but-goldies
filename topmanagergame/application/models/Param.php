<?php
/**
 * Stores whole constants based on config.yaml file
 * @author Radosław Benkel 
 */
 
abstract class Model_Param
{
    /**
     * @var array
     */
    protected static $_constants;

    /**
     * @return array
     */
    public static function getDefaultParams()
    {
        $tmp = new Zend_Config_Yaml(APPLICATION_PATH . '/configs/gameParams.yaml');
        return $tmp->toArray();
    }

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public static function set($data)
    {
        if (is_array($data) == false)
            throw new InvalidArgumentException('Data parameter must be an array');

        self::$_constants = $data;
    }

    /**
     * Returns config value, use . delimeter
     *
     * Example
     * Model_Param::get('test.value.1');
     * will return $data['test']['value']
     *
     * Wse null value to return whole config array;
     *
     * @static
     * @param  $path string
     * @return mixed
     */
    public static function get($path = null)
    {
        if (self::$_constants == null)
            self::$_constants = self::getDefaultParams();

        if ($path == null)
            return self::$_constants;

        $keys = explode('.', $path);
        $value = self::$_constants;
        foreach ($keys as $key) {
            if (isset($value[$key]) == false)
                throw new Exception("Value for path $path doesn't exists or it's null");

            $value = $value[$key];
        }
        return $value;
    }

    /**
     * @return void
     */
    public static function reset()
    {
        self::$_constants = null;
    }
}
