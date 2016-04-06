qx.Class.define("frontend.lib.io.HttpRequest",
{
    extend : qx.io.request.Xhr,

    include : [
        frontend.MMessage
    ],

    properties :
    {
        showLoadingDialog :
        {
            check : "Boolean",
            init : true
        },

        showErrorDetails :
        {
            check : "Boolean",
            init : true
        }
    },

    construct : function(vUrl, vMethod)
    {
        this.base(arguments, vUrl);

        this.setAccept("application/json");

        if (vMethod) {
            this.setMethod(vMethod);
        }

        this.addListener("fail", this._onError, this);

        this.addListener("fail", this.__closeDialog, this);
        this.addListener("success", this.__closeDialog, this);
        this.addListener("abort", this.__closeDialog, this);
    },

    members :
    {
        __dialog : null,

        _onError : function(e)
        {
            var error;
            if (this.getShowErrorDetails()) {
                try {
                    error = this.getResponseJson().message;
                } catch (ex) {
                    error = this.getResponseText();
                }
            }
            if (error == '#logout#') {
                var login = frontend.app.Login.getInstance();
                login.setUserInfo(null);
                login.fireEvent("logout");
                return;
            }
            this.showError(error || "io.request:error" + this.getStatus());
        },

        send : function()
        {
            if (this.getShowLoadingDialog()) {
                this.__openDialog();
            }
            this.base(arguments);
        },

        __openDialog : function()
        {
            if (this.__dialog === null) {
                this.__dialog = new frontend.lib.dialog.Dialog(Tools.tr("io.request:please wait")).set({
                    caption : Tools.tr("io.request:loading"),
                    width : 250
                });
            }
            this.__dialog.open();
        },

        __closeDialog : function()
        {
            if (this.__dialog) {
                this.__dialog.close();
            }
        },
        
        getResponseJson : function()
        {
            try {
                return JSON.parse(this.getResponseText());
            } catch (ex) {
                return null;
            }
        }
    }
});