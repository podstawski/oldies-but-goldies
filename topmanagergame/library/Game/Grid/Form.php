<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Game_Grid_Form extends Bvb_Grid_Form
{
    protected $_subFormDecorator = array('FormElements',
                                         array('HtmlTag',
                                               array('tag'   => 'table',
                                                     'class' => 'table-area subform'
                                               )
                                         )
    );
}