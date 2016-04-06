qx.Class.define("frontend.lib.ui.form.CheckBox",
{
    extend : qx.ui.form.CheckBox,

    properties :
    {
        executable :
        {
            check : "Boolean",
            init : true
        }
    },

    members :
    {
        _onExecute : function()
        {
            if (this.getExecutable()) {
                this.toggleValue();
            }
        }
    }
});