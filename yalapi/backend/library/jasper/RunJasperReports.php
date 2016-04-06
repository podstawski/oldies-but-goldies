<?php

/**
 * Executes command line jasper reports with db access
 */

class RunJasperReports
{
    protected $_dbtype,
              $_dbhost,
              $_dbport,
              $_dbname,
              $_dbuser,
              $_dbpass;

    protected $_validDBAdapters = array('postgresql');

    /**
     * @param $dbtype string adapter @see _validDBAdapters
     * @param $dbhost string database host
     * @param $dbport string database port
     * @param $dbname string database name
     * @param $dbuser string database username
     * @param $dbpass string database password
     */
    public function __construct($dbtype, $dbhost, $dbport, $dbname, $dbuser, $dbpass)
    {
        if (!in_array($dbtype, $this->_validDBAdapters)) {
            throw new InvalidArgumentException('Adapter "' . $dbtype . '" is not supported');
        }

        $this->_dbtype = $dbtype;
        $this->_dbhost = $dbhost;
        $this->_dbport = $dbport;
        $this->_dbname = $dbname;
        $this->_dbuser = $dbuser;
        $this->_dbpass = $dbpass;
    }
    
    public function generatePDF($path, array $params = array())
    {
	return $this->generate('pdf',$path,$params);
    }

    /**
     * @param $type pdf | xls
     * @param $path Path to .jrxml file
     * @param array $params Associative array with params which will be pased to report.
     * @return object Containts properties: dir (working dir), cmd (run command), output (STDOUT & STDERR output) and pdf (PDF content)
     * @throws InvalidArgumentException|LogicException
     */
    public function generate($type, $path, array $params = array())
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Report file $path doesn't exsists");
        }

        $dir = __DIR__;

	if (!isset($params['SUBREPORT_DIR'])) $params['SUBREPORT_DIR']=dirname($path).'/';

	
        $filename = sys_get_temp_dir().'/jasper'.time().rand(1000,9999);

        $cmd = "java -jar RunJasperReports.jar -dbname " . $this->_dbname . " -dbhost " . $this->_dbhost . ':' . $this->_dbport . " -dbuser " . $this->_dbuser . " -dbpass " . $this->_dbpass . " -dbtype " . $this->_dbtype;
	$cmd.= " -folder " . sys_get_temp_dir() . " -filename " . $filename . " -output ". $type ." -reports " . $path;

        if (count($params)) {
            //RB treat each param as a string, could be done using closures, but then PHP >= 5.3 only.
            $tempParams = array();
            foreach ($params as $key => $value) {
	    	$type=is_numeric($value)?'integer':'string';
                $tempParams[] = sprintf('%s=%s:%s', $key, $type, urlencode($value));
            }
            $params = $tempParams;
            unset($tempParams);

            $cmd .= " -params '" . implode(',', $params) . "'";
        }

        //RB redirect STDERR to STDOUT
        $cmd .= ' 2>&1';

	$pwd=getcwd();
        //chdir(sys_get_temp_dir());
        chdir($dir);

	//die($cmd);

        $filename = $output = '';
        $handle = popen($cmd, 'r');
        while (($txt = fgets($handle)) !== FALSE)
        {
            $output .= $txt;
            //RB extract filename from output
	    $token='Going to generate report';
            if (strstr($txt, $token)) {
                $filename = trim(substr($txt, strlen($token)));
            }
        }
        pclose($handle);
	chdir($pwd);

        
        $result = (object)compact('dir', 'cmd', 'output');

        if (file_exists($filename)) {
            $result->pdf = file_get_contents($filename);
            unlink($filename);
        } else {
            throw new LogicException('There was an error while generating template'."\n\n$output");
        }

        return $result;
    }
}

	
