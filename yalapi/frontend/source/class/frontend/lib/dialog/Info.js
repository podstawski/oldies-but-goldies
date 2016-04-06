/* *********************************

#asset(qx/icon/${qx.icontheme}/32/actions/help-faq.png)

********************************** */

qx.Class.define("frontend.lib.dialog.Info",
{
    extend : frontend.lib.dialog.Error,

    construct : function(message)
    {
        this.base(arguments, message, "icon/48/actions/help-faq.png");
    }
});