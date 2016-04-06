qx.Class.define("frontend.lib.util.format.DateFormat",
{
    extend : qx.util.format.DateFormat,

    construct : function(format)
    {
        this.base(arguments, format || "yyyy-MM-dd HH:mm:ss");
    },

    members :
    {
        format : function(date, format)
        {
            if (format == null || format == this.__format) {
                return date;
            }

            if (this.__self == null) {
                this.__self = new frontend.lib.util.format.DbDateFormat();
            }

            var helper = new qx.util.format.DateFormat(format);
            var d = date instanceof Date ? date : this.parse(date);

            return helper.format(d);
        }
    }
});