qx.Class.define("frontend.lib.ui.form.NumberField",
{
    extend : qx.ui.form.Spinner,

    implement : [
        qx.ui.form.INumberForm,
        qx.ui.form.IRange,
        qx.ui.form.IForm
    ],

    include : [
        qx.ui.form.MForm
    ],

    construct : function()
    {
        this.base(arguments);

        this.getChildControl("textfield").set({decorator:null});
        this.getChildControl("upbutton").exclude();
        this.getChildControl("downbutton").exclude();
    },

    members :
    {
        _checkValue : function(value)
        {
            return new RegExp("^[0-9]+$").test(value) && value >= this.getMinimum() && value <= this.getMaximum();
        }
    }
});