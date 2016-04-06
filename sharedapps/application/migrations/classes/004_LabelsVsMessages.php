<?php
class LabelsVsMessages extends Doctrine_Migration_Base {

	private $_tableName1 = 'message_labels';
	private $_fkName1_1 = 'fk_message_label_labels';
	private $_fkName1_2 = 'fk_message_label_messages';

	public function up() {

		$this->createTable(
			$this->_tableName1,
			array(
				'id' => array(
					'type' => 'integer',
					'notnull' => true,
					'primary' => true,
					'autoincrement' => true,
				),
				'label_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
				'message_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
			)
		);





		$this->createForeignKey(
			$this->_tableName1,
			$this->_fkName1_1,
			array(
				'local' => 'message_id',
				'foreign' => 'id',
				'foreignTable' => 'messages',
				'onDelete' => 'CASCADE',
			)
		);

		$this->createForeignKey(
			$this->_tableName1,
			$this->_fkName1_2,
			array(
				'local' => 'label_id',
				'foreign' => 'id',
				'foreignTable' => 'labels',
				'onDelete' => 'CASCADE',
			)
		);



	}



	public function down()
	{
		$this->dropTable($this->_tableName1);
	}
}

