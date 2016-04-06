qx.Class.define("frontend.lib.util.format.DbDateFormat",
{
    extend : frontend.lib.util.format.DateFormat,

    construct : function()
    {
        this.base(arguments, "dd-MM-yyyy HH:mm:ss");
    }
});