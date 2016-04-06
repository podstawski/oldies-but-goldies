qx.Class.define("frontend.lib.list.dynamic.Abstract",
{
    extend : qx.ui.container.Composite,

    include : [
        frontend.lib.util.MGetSource
    ],

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-table-list"
        },

        id :
        {
            init : null,
            nullable : true,
            event : "changeId",
            apply : "_applyId"
        }
    },

    events :
    {
        "changeId" : "qx.event.type.Data",
        "makeTableFinish" : "qx.event.type.Event"
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox(10));

        var toolbar = this.getChildControl("toolbar");
        var content = this.getChildControl("content");

        this.add(toolbar);
        this.add(content, {flex:1});

        this._request = new frontend.lib.io.HttpRequest;
        this._request.addListener("success", this._onRequestSuccess, this);
    },

    members :
    {
        _request : null,
        
        __table : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "label":
                    control = new qx.ui.basic.Label().set({
                        alignY : "middle"
                    });
                    control.bind("value", control, "visibility", {
                        converter : function (value) {
                            return !!value ? "visible" : "excluded"
                        }
                    });
                    break;

                case "combotable":
                    control = new frontend.lib.ui.form.ComboTable().set({
                        dataUrl    : this._dataUrl,
                        dataColumn : this._dataColumn,
                        minWidth   : 200
                    });
                    control.addListener("changeModel", function(e){
                        this.setId(e.getData());
                    }, this);
//                    control.bind("model", this, "id");
                    break;

                case "toolbar":
                    control = new qx.ui.toolbar.ToolBar;
                    control.set({
                        appearance : "ui-toolbar"
                    });
                    control.add(this.getChildControl("label"));
                    control.add(this.getChildControl("combotable"));
                    break;

                case "content":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
                    break;

            }

            return control || this.base(arguments, id);
        },

        _applyId : function(value, old)
        {
            if (value) {
                this.sendRequest();
            }
        },

        sendRequest : function()
        {
            this._request.send();
        },

        _onRequestSuccess : function()
        {
            var data = this._request.getResponseJson();
            if (data) {
                var table   = this._makeTable(data);
                var content = this.getChildControl("content");
                if (this.__table) {
                    content.remove(this.__table);
                }
                content.add(this.__table = table, {flex:1});
                this.fireEvent("makeTableFinish");
            }
        },

        _makeTable : function(data)
        {
            throw new Error("Abstract _makeTable method call!");
        }
    }
});