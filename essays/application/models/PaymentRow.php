<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_PaymentRow extends GN_Model_PaymentRow
{
    public function getItemNumber() {
        $data = $this->getCustomData();
        return $data['item_number'];
    }

    public function getSubjectID() {
        $data = $this->getCustomData();
        return $data['subject_id'];
    }
}
