<?php

class Report extends AppModel
{
    static $table_name = 'reports';

    static $validates_presence_of = array(
        array('name'),
        array('path'),
        array('parent_id') //it's imposible to add base template using application
    );
}
