<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_DomainsRow extends GN_Model_DomainRow
{
    /**
     * @var mixed
     */
    public $data;

	public function init()
	{
		parent::init();
		$this->data = unserialize(base64_decode($this->settings));
	}

	public function save()
	{
		$this->settings = base64_encode(serialize($this->data));
		return parent::save();
	}
}
