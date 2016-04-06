<?php
class ClassGroup_Process
{
    public static function generateProcessId()
    {
        $_SESSION['process-id'] = mt_rand();
        return $_SESSION['process-id'];
    }

    public static function confirmProcessId($processId)
    {
        if (empty($_SESSION['process-id'])) {
            return false;
        }
        return $_SESSION['process-id'] == $processId;
    }

    public static function discardProcessId()
    {
        $_SESSION['process-id'] = false;
    }
}
