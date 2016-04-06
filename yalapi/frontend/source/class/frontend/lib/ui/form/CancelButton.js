qx.Class.define("frontend.lib.ui.form.CancelButton",
{
    extend : frontend.lib.ui.form.Button,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "button-cancel"
        }
    }
});