qx.Class.define("frontend.lib.ui.form.TextField",
{
    extend : qx.ui.form.TextField,

    properties :
    {
        liveUpdate :
        {
            refine : true,
            init : true
        }
    }
});