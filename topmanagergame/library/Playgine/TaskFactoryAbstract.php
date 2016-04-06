<?php
/**
 * @author Radosław Benkel
 */
 
abstract class Playgine_TaskFactoryAbstract
{
    /**
     * Defines if its a task or event factory
     * @var string Task|Event
     */
    protected static $_type;

    /**
     * Maps id form database to correct task class
     * @var array
     */
    protected static $_classMap;

    /**
     * @static
     * @throws InvalidArgumentException|UnexpectedValueException
     * @param  $id
     * @return Playgine_Task_Abstract/Playgine_Event_Abstract
     */
    public static function factory($id)
    {
        if (is_string($id)) {
            if ($taskId = array_search($id, static::$_classMap)) {
                $task = static::_createObject($id);
                $task->setTaskName($id);
                return $task->setTaskId($taskId);
            } else {
                throw new UnexpectedValueException(static::$_type . ' object matching name "' . $id . '" not found in ' . __CLASS__);
            }
        } else if (is_int($id)) {
            if (array_key_exists($id, static::$_classMap)) {
                $task = static::_createObject(static::$_classMap[$id]);
                $task->setTaskName(static::$_classMap[$id]);
                return $task->setTaskId($id);
            } else {
                throw new UnexpectedValueException(static::$_type . ' object matching id = ' . $id . ' not found in ' . __CLASS__);
            }
        } else {
            throw new InvalidArgumentException('You should provide string or int as parameter');
        }
    }

	/**
	 * @static
	 * @param  $name
	 * @return Playgine_Task_Abstract|Playgine_Event_Abstract
	 */
    private static function _createObject($name)
    {
        $taskName = 'Playgine_' . static::$_type . '_' . $name;
        $taskObject = new $taskName();
        return $taskObject;
    }

	public static function getTaskNameByType($id)
	{
		return isset(static::$_classMap[$id]) ? static::$_classMap[$id] : null;
	}

	public static function getTaskTypeByName($name)
	{
		return array_search($name, static::$_classMap) ?: null;
	}

}
