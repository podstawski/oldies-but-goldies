qx.Class.define("frontend.lib.ui.form.SubmitButton",
{
    extend : frontend.lib.ui.form.Button,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "button-submit"
        }
    }
});