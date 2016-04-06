<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_Admin_Schools extends Game_Grid
{
    public function init()
    {
        $this->setSource(
            new Bvb_Grid_Source_Zend_Table(
                new Model_School()
            )
        );

        $this->setNoFilters(false);

        $form = new Game_Grid_Form();
        $form->setAdd(true)
             ->setEdit(true)
             ->setDelete(true);

        $form->setAddButton(true);

        $this->setForm($form);
    }
}