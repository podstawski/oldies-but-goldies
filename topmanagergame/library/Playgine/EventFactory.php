<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
abstract class Playgine_EventFactory extends Playgine_TaskFactoryAbstract
{
    protected static $_type = 'Event';
    
    /**
     * Maps id form database to correct task class
     * @var array
     */
    protected static $_classMap = array(
        
    );
}
