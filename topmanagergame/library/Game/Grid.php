<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Game_Grid extends Bvb_Grid_Deploy_Table
{
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->setImagesUrl($this->getView()->baseUrl() . '/images/grid/');
        $this->addTemplateDir('Game/Grid/Template', 'Game_Grid_Template', 'table');
        $this->addTemplateParam(
            'cssClass', array(
                             'table' => 'table-area table-grid'
                        )
        );
        $this->setExport(array());
        $this->setNoFilters(true);

        $this->init();
    }

    public function init()
    {

    }

    /**
     * @return Game_Grid
     */
    protected function hideColumns($colName)
    {
        foreach (func_get_args() as $colName) {
            if ($this->_getColumn($colName) !== null) {
                $this->updateColumn($colName, array('hidden' => true));
            }
        }
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : null;
    }
}