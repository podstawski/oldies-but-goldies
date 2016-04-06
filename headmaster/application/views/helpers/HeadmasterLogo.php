<?php

class Zend_View_Helper_HeadmasterLogo extends Zend_View_Helper_Abstract
{ 
   
    public function HeadmasterLogo() 
    {
        ?>
        <a href="<?php echo $this->view->baseUrl('/');?>"><img src="<?php echo $this->view->baseUrl();?>/img/headmaster_logo.png" alt="<?php echo $this->view->translate('HeadMaster'); ?>" class="logo" width="287" height="48" /></a>
        <?php
    }

}
