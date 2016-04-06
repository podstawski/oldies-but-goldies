<?php
/**
 * Simple class to load DB fixtures from given directory. 
 * @author Radosław Benkel 
 */
 
class FixtureLoader
{
    const SQL_INSERT_STRING = 'INSERT INTO %s (%s) VALUES (%s)';
    const REQUIRE_PREG = '/requires ([a-z_]+\.php)/';
    const DATA_PREG = '/return array/';
    
    private $_pdo;

    protected $_fixturesPath;
    protected $_loadedFiles = array();

    public function __construct(PDO $pdo, $fixturesPath)
    {
        $this->_pdo = $pdo;

        //force triangling coma
        if ($fixturesPath[strlen($fixturesPath) - 1] !== '/') {
            $fixturesPath .= '/';
        }

        if (!file_exists($fixturesPath)) {
            throw new InvalidArgumentException("Fixture path '$fixturesPath' doesn't exists");
        }
        $this->_fixturesPath = $fixturesPath;
    }

    public function applyFixtures()
    {
        $files = glob($this->_fixturesPath . '*.php');
        foreach ($files as $file) {
            $this->_processFixtureFile($file);
        }

        if (count($this->_loadedFiles) > 0) {
            return "Loaded fixtures for tables: " . implode(", ", $this->_loadedFiles);
        } else {
            return 'No fixtures found';
        }
    }

    protected function _processFixtureFile($file)
    {
        $tableName = basename($file, '.php');

        if (array_search($tableName, $this->_loadedFiles) !== false) {
            return;
        }

        //RB check dependencies
        foreach(file($file) as $line) {
            preg_match(self::REQUIRE_PREG, $line, $hits);
            if ($hits) {
                $this->_processFixtureFile($this->_fixturesPath . $hits[1]);
            }

            preg_match(self::DATA_PREG, $line, $hits);
            if (count($hits)) {
                break;
            }
        }

        $data = include $file;

        //RB call closure if it possible and pass $dbh
        if (is_callable($data)) {
            call_user_func($data, $this->_pdo);
            $this->_loadedFiles[] = $tableName;
            return;
        }

        $paramNames = $data[0];
        $paramCount = count($paramNames);
        next($data);

        $paramNamesString = implode(', ', $paramNames);
        $paramValuesString = substr(str_repeat("?,", $paramCount), 0, -1);

        $sql = sprintf(self::SQL_INSERT_STRING, $tableName, $paramNamesString, $paramValuesString);

        try {
            $stmt = $this->_pdo->prepare($sql);

            while($values = current($data)) {
                $success = $stmt->execute($values);
                if (!$success) {
                    $errorData = $this->_pdo->errorInfo();
                    throw new PDOException('File: ' . $file . ' => ' . $errorData[2]);
                }
                next($data);
            }
        } catch (PDOException $e) {
            throw $e;
        }

        $this->_loadedFiles[] = $tableName;
    }
}
