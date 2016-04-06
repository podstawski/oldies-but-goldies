qx.Class.define("frontend.app.source.Source",
{
    extend : qx.core.Object,

    events :
    {
        "changeData" : "qx.event.type.Data",
        "changeUrl" : "qx.event.type.Data",
        "changeDataKey" : "qx.event.type.Data"
    },

    properties :
    {
        data :
        {
            init : null,
            transform : "_transformData",
            event : "changeData"
        },

        url :
        {
            check : "String",
            init : null,
            nullable : true,
            apply : "_applyUrl",
            event : "changeUrl"
        },

        dataKey :
        {
            check : "String",
            init : "label",
            nullable : false,
            event : "changeDataKey"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this._request     = null;
        this.__cachedData = {};
    },

    members :
    {
        _request : null,

        __cachedData : null,

        _applyUrl : function(url, old)
        {
            if (old) {
                this._request.removeListener("success", this._onRequestSuccess, this);
            }

            this._request = null;

            if (url) {
                this._request = new frontend.lib.io.HttpRequest(url, "GET");
                this._request.addListener("success", this._onRequestSuccess, this);
                this._request.send();
            }
        },

        _onRequestSuccess : function()
        {
            var data = this._request.getResponseJson();
            this.setData(data);
        },

        _transformData : function(data)
        {
            return data;
        },

        reload : function()
        {
            if (this._request) {
                this._request.send();
            }
        },

        getByKey : function(value, key)
        {
            key = key || "id";

            if (this.__cachedData[key] == null) {
                this.__cachedData[key] = {};
            }
            
            if (this.__cachedData[key][value] == null) {
                var data = this.getData();
                if (data) {
                    data.forEach(function(dataEntry){
                        if (dataEntry[key] == value) {
                            this.__cachedData[key][value] = dataEntry;
                            return;
                        }
                    }, this);
                }
            }

            return this.__cachedData[key][value];
        },

        getById : function(id)
        {
            return this.getByKey(id, "id");
        },

        getByLabel : function(label)
        {
            return this.getByKey(label, "label");
        }
    }
});