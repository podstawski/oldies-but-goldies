qx.Mixin.define("frontend.MMessage",
{
    members :
    {
        showMessage : function(message)
        {
            var dialog = new frontend.lib.dialog.Message(message);
        },
        showError : function(message)
        {
            var dialog = new frontend.lib.dialog.Error(message);
        }
    }
});