<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Game_Grid_Template_Table extends Bvb_Grid_Template_Table
{
    public function formMessage($sucess, $message)
    {
        return '<div class="messages"><ul><li>' . $message . '</li></ul></div>';
    }

    public function titlesStart()
    {
        return "    <thead><tr>";
    }

    public function titlesEnd()
    {
        return "    </tr></thead>" . PHP_EOL;
    }
}