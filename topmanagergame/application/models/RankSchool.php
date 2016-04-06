<?php

class Model_RankSchool extends Zend_Db_Table_Abstract
{
	protected $_name = 'rank_school';

    protected $_referenceMap = array(
        'Model_School' => array(
            'columns' => 'school_id',
            'refTableClass' => 'Model_School',
            'refColumns' => 'id'
        )
    );
}
