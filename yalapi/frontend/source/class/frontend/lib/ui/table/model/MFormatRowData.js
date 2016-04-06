qx.Mixin.define("frontend.lib.ui.table.model.MFormatRowData",
{
    construct : function()
    {
        var oldGetRowData = this.getRowData;
        this.getRowData = qx.lang.Function.bind(function(rowIndex){
            return this.__formatRowData(
                oldGetRowData.call(this, rowIndex)
            );
        }, this);

        this.getValue = qx.lang.Function.bind(function(columnIndex, rowIndex){
            var rowData = this.getRowData(rowIndex);
            if (rowData) {
                return this.__formatRowData(rowData)[this.getColumnId(columnIndex)];
            }
            return "";
        }, this);

        this.getValueById = qx.lang.Function.bind(function(columnId, rowIndex){
            var rowData = this.getRowData(rowIndex);
            if (rowData) {
                return this.__formatRowData(rowData)[columnId];
            }
            return "";
        }, this);
    },

    members :
    {
        __formatters : null,

        setDataFormatter : function(key, formatter, self)
        {
            if (this.__formatters === null) {
                this.__formatters = {};
            }
            this.__formatters[key] = [ formatter, self ];
        },

        removeDataFormatter : function(key)
        {
            delete this.__formatters[key];
        },

        getDataFormatter : function(key)
        {
            try {
                return this.__formatters[key][0];
            } catch (ex) {}
        },

        __formatRowData : function(rowData)
        {
            if (rowData) {
                if (!rowData.__formatted && this.__formatters) {
                    qx.lang.Object.getKeys(this.__formatters).forEach(function(key){
                        var formatter = this.__formatters[key][0];
                        var context   = this.__formatters[key][1];
                        rowData[key] = formatter.call(context || this, rowData);
                    }, this);
                }
                rowData.__formatted = true;
            }
            return rowData;
        }
    }
});