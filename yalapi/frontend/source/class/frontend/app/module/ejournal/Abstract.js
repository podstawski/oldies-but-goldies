qx.Class.define("frontend.app.module.ejournal.Abstract",
{
    type : "abstract",
    
    extend : qx.ui.container.Composite,

    events :
    {
        "makeTableFinish" : "qx.event.type.Event"
    },

    construct : function(main)
    {
        this.base(arguments, new qx.ui.layout.VBox);

        this.__main   = main;
        this.__table  = null;

        this._request = new frontend.lib.io.HttpRequest;
        this._request.addListener("success", this._onRequestSuccess, this);

//        this.addListener("appear", this.reloadData, this);
    },

    members :
    {
        _url : null,

        _request : null,
        
        __main : null,

        __table : null,

        getTable : function()
        {
            return this.__table;
        },

        reloadData : function()
        {
            var unitID = this.__main.getUnitId();
            if (unitID != null)
            {
                this._request.setUrl(Urls.resolve(this._url, unitID));
                this._request.send();
            }
        },

        _makeTable : function(data)
        {
            throw new Error("Method _makeTable not implemented!");
        },

        _onRequestSuccess : function()
        {
            var data = this._request.getResponseJson();
            if (data) {
                var table = this._makeTable(data);
                table.getSelectionModel().setSelectionMode(qx.ui.table.selection.Model.NO_SELECTION);
                if (this.__table) {
                    this.remove(this.__table);
                    this.__table.dispose();
                    this.__table = null;
                }
                this.add(this.__table = table, {flex:1});
                this.fireEvent("makeTableFinish");
            }
        }
    }
});