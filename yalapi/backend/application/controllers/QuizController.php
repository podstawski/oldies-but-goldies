<?php

class QuizController extends Zend_Controller_Action
{
    protected $_amfServer;
    public function init()
    {
        require_once "ZendAmfServiceBrowser.php";
        $this->_amfServer = new Zend_Amf_Server();
        $this->_amfServer->setClass('ZendAmfServiceBrowser');
        ZendAmfServiceBrowser::$ZEND_AMF_SERVER = $this->_amfServer;
    }
    
    public function scoresAction()
    {
        $this->_amfServer->setClass(new Service_Quiz());
        echo $this->_amfServer->handle();
        die();
    }

    public function questionsAction()
    {
        $amount = $this->_getParam('items', null);

        $path = $this->getInvokeArg('bootstrap')->getOption('questions');
        $path = $path['path']['xml'];

        $service = new Service_Question($path);
        $this->_response->setBody(base64_encode($service->fetch($amount)));
    }

    public function updateQuestionsAction()
    {
        $path = $this->getInvokeArg('bootstrap')->getOption('questions');
        $path = $path['path'];

        //TODO - refactor for uploading questions.csv
        $output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<questions>";
        
        $map = array(
            0 => 'subject',
            1 => 'answer1',
            2 => 'answer2',
            3 => 'answer3',
            4 => 'answer4',
            5 => 'correct_answer',
            6 => 'hint',
            7 => 'school_type',
            8 => 'answer5',
            9 => 'answer6',
            10 => 'program_type',
            13 => 'correct_answer_opinion_ratio'
        );

        if (($handle = fopen($path['csv'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $output .= "\t<question>\n";
                foreach ($map as $key => $keyValue) {
                    if ($data[$key]) {
                        $output .= sprintf("\t\t<%s>%s</%s>\n", $keyValue, $data[$key], $keyValue);
                    }
                }
                $output .= "\t</question>\n";
            }
            fclose($handle);
        }

        $output .= '</questions>';

        file_put_contents($path['xml'], $output);
        die("File converted");
    }
}
