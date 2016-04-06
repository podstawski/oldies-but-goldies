qx.Class.define("frontend.app.module.user.Account",
{
    type : "singleton",
    
    extend : frontend.lib.form.Abstract,

    construct : function()
    {
        this.base(arguments);

        this.setCaption("Edytowanie danych o koncie");

        this.__request = new frontend.lib.io.HttpRequest;
        this.__request.addListener("success", this._onRequestSuccess, this);

        var form = this.getForm();
        form.setSubmitAfterValidation(false);

        var newPasswordItem    = form.getItem("new_password");
        var retypePasswordItem = form.getItem("retype_password");
        var that = this;

        this.__pvm = new frontend.lib.ui.form.validation.Manager();
        this.__pvm.add(newPasswordItem, [ Validate.slength(4, 256) ], this);
        this.__pvm.add(retypePasswordItem, [ Validate.slength(4, 256), function() {
            if (newPasswordItem.getValue() != retypePasswordItem.getValue()) {
                throw new qx.core.ValidationError("Validation Error", "Hasła nie zgadzają się");
            }
            return true;
        } ], this);

        this.addListener("appear", this.reloadData, this);
    },

    members :
    {
        _url        : "USERS",
        _prefix     : "user.account",
        _template   :
        {
            username : {
                type : "TextField",
                properties : {
                    required : true,
                    readOnly : true,
                    enabled  : false
                }
            },
            email : {
                type : "TextField",
                properties : {
                    required : true,
                    maxLength : 256
                },
                validators : [
                    Validate.email()
                ]
            },
            new_password : {
                type : "PasswordField",
                properties : {
                    maxLength : 256
                },
                validators : [
                    Validate.string()
                ]
            },
            retype_password : {
                type : "PasswordField",
                properties : {
                    maxLength : 256
                },
                validators : [
                    Validate.string()
                ]
            }
        },

        __userID : null,
        
        __request : null,

        _onComplete : function()
        {
            if (this.getForm().getItem("new_password").getValue() != null) {
                this.__pvm.addListenerOnce("complete", function(e){
                    if (this.__pvm.isValid()) {
                        this._save();
                    }
                }, this);
                this.__pvm.validate();
            } else {
                this._save();
            }
        },

        _save : function()
        {
            var data = this.getForm().getValues();
            this.__request.setRequestData(data);
            this.__request.setMethod("PUT");
            this.__request.send();
        },

        _onRequestSuccess : function()
        {
            var data = this.__request.getResponseJson();
            if (data) {
                if (data.message != null) {
                    this.showError(data.message);
                } else if (data.id != null) {
                    this._rowData = data;
                    var form = this.getForm();
                    form.populate(qx.lang.Object.merge(data, {
                        new_password    : null,
                        retype_password : null
                    }));
                    var googleapps = window.yala.googleapps || {};
                    var domain = frontend.app.Login.getDomain();
                    var profile_editable = !domain || (domain && googleapps.profile_editable);
                    form.getItem("username").setEnabled(false);
                    ["new_password", "retype_password", "email", "submit"].forEach(function(name){
                        form.getItem(name).setEnabled(profile_editable);
                    }, this);
                    if (this.__request.getMethod() == "PUT") {
                        this.showMessage("Zmiany zostały zapisane!");
                        this.close();
                    }
                }
            }
        },

        reloadData : function()
        {
            this.__request.resetRequestData();
            this.__request.setMethod("GET");
            this.__request.send();
        },

        open : function(userID)
        {
            this.__userID = userID || frontend.app.Login.getId();
            this.__request.setUrl(Urls.resolve(this._url, this.__userID));

            this.base(arguments);
        }
    }
});