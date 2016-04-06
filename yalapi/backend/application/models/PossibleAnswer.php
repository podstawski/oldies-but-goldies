<?php

class PossibleAnswer extends AppModel
{
    static $table_name = 'survey_possible_answers';

    static $validates_presence_of = array(
        array('content'),
    );

    static $belongs_to = array(
        array('question')
    );
}
