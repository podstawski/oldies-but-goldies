qx.Class.define("frontend.lib.form.Abstract",
{
    type : "abstract",
    
    extend : frontend.lib.ui.window.Modal,

    include : [
        frontend.MMessage
    ],

    events :
    {
        "completed" : "qx.event.type.Event"
    },
    
    construct : function(rowData)
    {
        this.base(arguments);
        var caption = this._caption || (this._prefix ? (this._prefix + ":window " + (rowData == null ? "add" : "edit")) : "");

        this.setCaption(Tools['tr'](caption));
        this.setMinWidth(400);

        var template = qx.lang.Object.carefullyMergeWith(qx.lang.Object.clone(this._template), {
            cancel: {
                type : "CancelButton",
                properties: {
                    label : Tools["tr"]((this._prefix || "form") + ":button cancel")
                }
            },
            submit : {
                type : "SubmitButton",
                properties : {
                    label : Tools["tr"]((this._prefix || "form") + ":button " + (rowData == null ? "add" : "edit"))
                }
            }
        });
        this._form = frontend.lib.ui.form.Form.create(template, this._prefix).set({
            url : Urls.resolve(this._url)
        });
        this._form.addListener("canceled", this.close, this);
        this._form.addListener("completed", this._onComplete, this);
        this._form.addListener("completed", function(){
            this.fireEvent("completed")
        }, this);

        this.add(this._form, {flex:1});

        if (rowData) {
            this._form.populate(rowData);
            if (rowData.id) {
                this._form.setMethod("PUT");
                this._form.setUrl(Urls.resolve(this._url, rowData.id));
            }
        }

        this._rowData = rowData;
    },

    members :
    {
        _caption    : null,
        _url        : null,
        _template   : null,
        _form       : null,
        _prefix     : null,
        _rowData    : null,

        getForm: function()
        {
            return this._form;
        },

        _onComplete : function(e)
        {
            if (this._prefix) {
                this.showMessage(Tools["tr"](this._prefix + ":" + (this._rowData == null ? "added" : "edited")));
                this.close();
                return;
            }

            throw new Error("Abstract _onComplete method call!");
        }
    }
});