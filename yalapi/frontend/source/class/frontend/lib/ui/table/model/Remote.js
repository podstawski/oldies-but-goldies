qx.Class.define("frontend.lib.ui.table.model.Remote",
{
    extend : qx.ui.table.model.Remote,

    include : [
        frontend.lib.ui.MSearchBox,
        frontend.lib.ui.table.model.MFormatRowData
    ],

    events :
    {
        "loadedRowCount"        : "qx.event.type.Event",
        "loadedRowCountFailed"  : "qx.event.type.Event",
        "loadedRowData"         : "qx.event.type.Event",
        "changeFilterKey"       : "qx.event.type.Data"
    },

    properties :
    {
        dataUrl :
        {
            check : "String",
            init : null
        },

        requestMethod :
        {
            check : [ "GET", "POST" ],
            init : "GET"
        },

        filterKey :
        {
            check : "String",
            init : null,
            event : "changeFilterKey"
        }
    },

    construct : function()
    {
        this.base(arguments);
        
        this.__sortColumnIndex = 0;
        this.__listenerIds     = [];
        this.__paramsMap       = {};
    },

    members :
    {
        getColumns  : function()
        {
            var columns = [];
            for (var i = 2; i < this.getColumnCount(); i++) {
                columns.push(this.getColumnName(i));
            }
            return columns;
        },

        getKeys : function()
        {
            var keys = [];
            keys = keys.concat(this.__columnIdArr);
            keys.splice(0,2);
            return keys;
        },

        _getRequest : function(params)
        {
            params = this._mergeParams(params);

            var method  = this.getRequestMethod();
            var request = new frontend.lib.io.HttpRequest().set({
                showLoadingDialog : false,
                method : method
            });

            if (method == "GET") {
                request.set({
                    url : qx.util.Uri.appendParamsToUrl(this.getDataUrl(), params)
                });
            } else {
                request.set({
                    url : this.getDataUrl(),
                    requestData : params
                });
            }
            return request;
        },

        _mergeParams : function(params)
        {
            return qx.lang.Object.merge(params || {}, this.__paramsMap, {
                "pager[search]" : this.getSearchValue() || ""
            });
        },

        _onChangeSearchValue : function(e)
        {
            this.reloadData();
        },

        _loadRowCount : function()
        {
            var request = this._getRequest({
                "pager[total_records]" : 1
            });
            request.addListenerOnce("success", function(e){
                var data = request.getResponseJson();
                
                if (data == null || !data.hasOwnProperty("total_records")){
                    this.fireEvent("loadedRowCountFailed");
                }else{
                    this._onRowCountLoaded(data.total_records);
                }

                this.fireEvent("loadedRowCount");
            }, this);
            request.send();
        },

        _loadRowData : function(firstRow, lastRow)
        {
            var request = this._getRequest({
                "pager[offset]" : firstRow,
                "pager[limit]"  : this.getBlockSize(),
                "pager[order]"  : this.getColumnId(this.__sortColumnIndex) ? (this.__sortAscending ? "" : "-") + this.getColumnId(this.__sortColumnIndex) : ""
            });
            request.addListenerOnce("success", function(e){
                var data = request.getResponseJson();
                this._onRowDataLoaded(data);
                this.fireEvent("loadedRowData");
            }, this);
            request.send();
        },

        __paramsMap : {},

        setParam : function(key, value)
        {
            this.__paramsMap["pager[" + key + "]"] = value;
            return this;
        },

        removeParam : function(key)
        {
            delete this.__paramsMap["pager[" + key + "]"];
            return this;
        },

        __filter : null,
        
        __listenerIds : null,

        connectToFilter : function(filter)
        {
            this.disconnectFromFilter();

            this.__filter = filter;
            this.__listenerIds = [
                [ this.__filter.addListener("changeFilterValue", this._onChangeFilterValue, this), this.__filter ],
                [ this.addListener("changeFilterKey", this._onChangeFilterKey, this), this]
            ];

            this.fireDataEvent("changeFilterKey", this.getFilterKey());
            
            return this;
        },

        disconnectFromFilter : function()
        {
            this.__listenerIds.forEach(function(dataEntry){
                dataEntry[1].removeListenerById.call(dataEntry[1], dataEntry[0]);
            }, this);
            this.__listenerIds = [];

            return this;
        },

        _onChangeFilterValue : function(e)
        {
            var id = e.getData();
            var filterKey = this.getFilterKey();
            if (filterKey) {
                if (id != null) {
                    this.setParam(filterKey, id);
                } else {
                    this.removeParam(filterKey);
                }
                this.reloadData();
            }
        },

        _onChangeFilterKey : function(e)
        {
            var value = e.getData();
            if (this.__filter) {
                this.__filter.setVisibility(value ? "visible" : "excluded");
            }
        },

        setColumnNamesById : function(columnNameMap)
        {
            for (var i = 0; i < this.__columnIdArr.length; ++i) {
                var columnIndex = this.__columnIdArr[i];
                if (columnNameMap[columnIndex] !== undefined) {
                    this.__columnNameArr[i] = columnNameMap[columnIndex];
                }
            }
        }
    }
});