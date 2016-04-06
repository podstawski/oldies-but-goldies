qx.Class.define("frontend.app.form.user.Edit",
{
    extend : frontend.app.form.user.Add,

    construct: function(rowData)
    {
        if (!Acl.hasRight("users.C")) {
            this._template.role_id.properties.enabled = false;
        }

        var googleapps = window.yala.googleapps || {};
        var domain = frontend.app.Login.getDomain();
        var profile_editable = Acl.hasRight("users.U") && (!domain || (domain && googleapps.profile_editable));

        ["plain_password", "first_name", "last_name", "email"].forEach(function(name){
            this._template[name].properties.enabled = profile_editable;
        }, this);

        this.base(arguments, rowData);
    },

    members :
    {
        _template :
        {
            username : {
                type : "TextField",
                properties : {
                    required : true,
                    enabled : false
                }
            },
            plain_password : {
                type : "PasswordField",
                properties: {

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
            this.showMessage("Zapisano dane użytkownika!");
            this.close();
        }
    }
});