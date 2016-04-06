<?php

class Question extends AppModel
{
    static $table_name = 'survey_questions';

    static $validates_presence_of = array(
        array('title', 'type'),
    );

    /*static $belongs_to = array(
        array('Survey')
    );  */
    static $has_many = array(
        array('possible_answers')
    );
}
