qx.Class.define("frontend.lib.ui.form.DateField",
{
    extend : qx.ui.form.DateField,

    members :
    {
        _setDefaultDateFormat : function()
        {
            this.setDateFormat(new qx.util.format.DateFormat("dd-MM-yyyy"));
        }
    }
});