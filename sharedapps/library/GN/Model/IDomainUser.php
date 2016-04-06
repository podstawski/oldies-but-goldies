<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

interface GN_Model_IDomainUser
{
    /**
     * @abstract
     * @return GN_Model_DomainRow
     */
    public function getDomain();

    /**
     * @abstract
     * @return string
     */
    public function getEmail();
}