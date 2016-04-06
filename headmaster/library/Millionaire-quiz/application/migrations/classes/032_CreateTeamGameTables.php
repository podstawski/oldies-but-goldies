<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTeamGameTables extends Doctrine_Migration_Base
{
    private $_tableName1 = 'team_games';
    private $_tableName2 = 'teams';
    private $_tableName3 = 'team_members';

    public function up()
    {
        $this->createTable($this->_tableName1, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'hash' => array(
                'type' => 'varchar(32)',
                'notnull' => true,
            ),
            'create_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'start_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'end_date' => array(
                'type' => 'timestamp',
                'notnull' => true
            ),
            'leader' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'teams' => array(
                'type' => 'smallint',
                'notnull' => true
            ),
            'team_members' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'winner_team_id' => array(
                'type' => 'integer',
                'notnull' => false
            ),
            'questions' => array(
                'type' => 'text',
                'notnull' => false
            ),
            'status' => array(
                'type' => 'smallint',
                'notnull' => true
            )
        ));

        $this->createTable($this->_tableName2, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'game_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'score' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
        ));

        $this->createTable($this->_tableName3, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'team_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'score' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
        ));
    }

    public function postUp()
    {

    }

    public function down()
    {
        $this->dropTable($this->_tableName3);
        $this->dropTable($this->_tableName2);
        $this->dropTable($this->_tableName1);
    }

}