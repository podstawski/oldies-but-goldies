<?php

class Zend_View_Helper_MakeUrl extends Zend_View_Helper_Abstract
{
    public function makeUrl($textID)
    {
        static $linksArray = array();

        if (!array_key_exists($textID, $linksArray)) {
            $linkModel = new Model_Url();
            if ($linkRow = $linkModel->fetchRow(array('text_id = ?' => $textID, 'active = 1'))) {
                $link = array();
                $link[] = '<a href="';
                $link[] = $linkRow->url;
                $link[] = '"';
                if ($linkRow->target) {
                    $link[] = ' target="' . $linkRow->target . '"';
                }
                if ($linkRow->title) {
                    $link[] = ' title="' . $linkRow->target . '"';
                }
                $link[] = '>';
                $link[] = $linkRow->text;
                $link[] = '</a>';
                $linksArray[$textID] = implode('', $link);
            } else {
                $linksArray[$textID] = '<a href="#">>' . $textID . '<</a>';
            }
        }
        return $linksArray[$textID];
    }
}
