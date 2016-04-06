qx.Class.define("frontend.lib.ui.form.validation.AsyncValidator",
{
    extend : qx.ui.form.validation.AsyncValidator,

    members :
    {
        __valid : null,
        
        setValid: function(valid, message)
        {
            this.__valid = valid;
            this.base(arguments, valid, message);
        },

        isValid : function()
        {
            return this.__valid;
        }
    }
});