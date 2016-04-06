qx.Class.define("frontend.app.form.Room",
{
    extend : frontend.lib.form.Abstract,

    construct : function(rowData)
    {
        this.base(arguments);

        if (typeof rowData !== "undefined")
            this._form.populate(rowData);
        
        var manager = qx.event.Registration.getManager(this._form);
        manager.removeAllListeners(this._form);
        
        this._form.addListener("canceled", this.close, this);
        this._form.addListener("completed", this._onComplete, this);
    },

    members :
    {
        _onComplete : function(e)
        {
            this.fireDataEvent("completed", [this._form.getValues()], this);
            this.close();
        },

        _url        : Urls.resolve("ROOMS"),
        _prefix     : "form.room",
        _template   :
        {
            name : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(2, 256)
                ]
            },
            symbol : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.slength(2, 256)
                ]
            },
            description : {
                type : "TextArea",
                properties : {
                    required: false,
                    autoSize : true
                }
            },
            available_space : {
                type : "Spinner",
                properties : {
                    required : true,
                    singleStep : 2,
                    maximum : Number.MAX_VALUE
                }
            }
        }
    }
});