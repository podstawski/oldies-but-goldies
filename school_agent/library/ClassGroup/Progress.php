<?php
class ClassGroup_Progress
{
    public static function start($progressID, $current = 0, $max = 1)
    {
        ClassGroup_Session::restore();
        $_SESSION['progress'][$progressID]['current'] = $current;
        $_SESSION['progress'][$progressID]['max'] = $max;
        $_SESSION['progress'][$progressID]['finished'] = false;
        ClassGroup_Session::stop();
    }

    public static function get($progressID)
    {
        if (empty($_SESSION['progress'][$progressID])) {
            return null;
        }
        return $_SESSION['progress'][$progressID];
    }

    public static function finish($progressID, $success = true)
    {
        ClassGroup_Session::restore();
        $_SESSION['progress'][$progressID]['current'] = $_SESSION['progress'][$progressID]['max'];
        $_SESSION['progress'][$progressID]['finished'] = true;
        $_SESSION['progress'][$progressID]['success'] = $success;
        ClassGroup_Session::stop();
    }

    public static function step($progressID, $step = 1)
    {
        ClassGroup_Session::restore();
        if ($_SESSION['progress'][$progressID]['current'] < $_SESSION['progress'][$progressID]['max']) {
            $_SESSION['progress'][$progressID]['current'] += $step;
        }
        ClassGroup_Session::stop();
    }

    public static function detachBrowser()
    {
        //odłącz od przeglądarki
        $urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
        $url = $urlHelper->url(array('controller' => 'progress', 'action' => 'ajax-detached'), null, true);
        if (getenv('APPLICATION_ENV') != 'development') {
            header('Location: ' . $url);
        }
        usleep(2000);
        echo '<script type="text/javascript">';
        echo 'function reload() {';
        echo 'window.location.href="' . $url . '";';
        echo '}';
        if (getenv('APPLICATION_ENV') != 'development') {
            echo 'setTimeout(reload,300);';
        }
        echo '</script>';
        for ($i = 0; $i < 4096; $i++) {
            echo ' ' . PHP_EOL;
        }
        flush();
        ob_end_flush();
        ini_set('max_execution_time', 0);
    }
}
