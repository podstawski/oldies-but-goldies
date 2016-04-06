<?php
class ChangeMessageIds extends Doctrine_Migration_Base {
	public function up() {
		Doctrine_Manager::connection()->execute('ALTER TABLE messages ALTER COLUMN message_id TYPE VARCHAR(255)');
		Doctrine_Manager::connection()->execute('REINDEX INDEX idxu_messages_id');
		Doctrine_Manager::connection()->execute('REINDEX INDEX messages_pkey');
	}

	public function down() {
		Doctrine_Manager::connection()->execute('ALTER TABLE messages ALTER COLUMN message_id TYPE VARCHAR(100)');
		Doctrine_Manager::connection()->execute('REINDEX INDEX idxu_messages_id');
		Doctrine_Manager::connection()->execute('REINDEX INDEX messages_pkey');
	}
}
