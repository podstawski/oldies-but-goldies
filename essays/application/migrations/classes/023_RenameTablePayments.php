<?php
class RenameTablePayments extends Doctrine_Migration_Base {
	public function up() {
		Doctrine_Manager::connection()->execute('ALTER TABLE payments RENAME TO payment');
	}

	public function down() {
		Doctrine_Manager::connection()->execute('ALTER TABLE payment RENAME TO payments');
	}
}
