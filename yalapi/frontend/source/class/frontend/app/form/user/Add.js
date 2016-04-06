qx.Class.define("frontend.app.form.user.Add",
{
    extend : frontend.lib.form.Abstract,

    construct : function(rowData)
    {
        if (frontend.app.Login.getDomain()) {
            this._template.email.properties.enabled = false;
        }
        if (this.classname.split(".").pop() == "Add") {
            rowData = {};
            rowData.role_id = 2;
        }
        this.base(arguments, rowData);
    },

    members :
    {
        _url      : "USERS",
        _prefix   : "form.user",
        _template :
        {
            username : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(4, 256),
                    Validate.regex(/^[a-zA-z0-9-_\.]+$/, Tools.tr('form.user.error.invalid_username'))
                ]
            },
            plain_password : {
                type : "PasswordField",
                properties: {
                    required : true
                }
            },
            first_name : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string()
                ]
            },
            last_name : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string()
                ]
            },
            email : {
                type: "TextField",
                properties : {
                    required : false
                },
                validators: [
                    Validate.email()
                ]
            },
            role_id : {
                type : "SelectBox",
                properties : {
                    source : "Roles"
                }
            }
        },

        _onComplete : function()
        {
            this.showMessage("Dodano nowego użytkownika!");
            this.close();
        }
    }
});