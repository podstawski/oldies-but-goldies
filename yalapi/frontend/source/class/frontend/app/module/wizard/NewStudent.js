qx.Class.define("frontend.app.module.wizard.NewStudent",
{
    extend : frontend.app.module.wizard.Abstract,

    construct : function()
    {
        this.base(arguments);

        var request = new frontend.lib.io.HttpRequest;
        request.setUrl(Urls.resolve("USER_PROFILE", frontend.app.Login.getId()));
        request.addListenerOnce("success", function(e){
            var data = request.getResponseJson();
            if (data.id) {
                this.setCurrentStep(2);
            }
        }, this);
        request.send();
    },

    members :
    {
        _steps :
        {
            "frontend.app.module.user.Profile" : "Wypełnij dane osobowe",
            "frontend.app.module.user.NewStudent" : "Wydrukuj kartę zgłoszeniową"
        }
    }
});