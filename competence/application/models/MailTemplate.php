<?php
class Model_MailTemplate extends GN_Model_MailTemplate
{
	public function init()
	{
		parent::init(array('project_id' => 'INT'));
	}
}
?>
