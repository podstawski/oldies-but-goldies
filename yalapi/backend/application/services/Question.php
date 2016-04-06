<?php
/**
 * Service for returning random questions
 * @author Radosław Benkel 
 */
 
class Service_Question
{
    const QUESTION_TAG = 'question';
    const RANDOM_QUESTION_AMOUNT = 10;

    protected $_path;

    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new LogicException("File $path not found");
        }
        $this->_path = $path;
    }

    /**
     * @param $items string
     * @return string xml content
     */
    public function fetch($items = null)
    {
        if (is_null($items)) {
            $items = self::RANDOM_QUESTION_AMOUNT;
        }
        $items = (int)$items;
        $document = new DOMDocument();
        $document->load($this->_path);
        $questionsList = $document->getElementsByTagName(self::QUESTION_TAG);

        $items = min($questionsList->length, $items);

        //RB length must be dynamic!
        while ($questionsList->length - $items > 0) {
            $itemNo = rand(0, $questionsList->length - 1);
            $item = $questionsList->item($itemNo);
            $item->parentNode->removeChild($item);
        }

        return $document->saveXML();
    }
}
