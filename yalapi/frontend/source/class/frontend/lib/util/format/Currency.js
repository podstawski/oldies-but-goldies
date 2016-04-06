qx.Class.define("frontend.lib.util.format.Currency",
{
    extend : qx.util.format.NumberFormat,

    properties :
    {
        groupingUsed :
        {
            refine : true,
            init : true
        },

        postfix :
        {
            refine : true,
            init : " zł"
        }
    },

    statics :
    {
        __self : null,

        format : function(value)
        {
            if (this.__self == null) {
                this.__self = new frontend.lib.util.format.Currency();
            }
            return this.__self.format(value);
        }
    }
});