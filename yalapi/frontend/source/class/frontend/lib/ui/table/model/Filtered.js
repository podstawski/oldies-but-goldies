qx.Class.define("frontend.lib.ui.table.model.Filtered",
{
    extend : qx.ui.table.model.Filtered,

    include : [
        frontend.lib.ui.MSearchBox,
        frontend.lib.ui.table.model.MFormatRowData
    ],

    properties :
    {
        delimiter :
        {
            check : "String",
            nullable : false,
            init : ";,"
        }
    },

    members :
    {
        _onChangeSearchValue : function()
        {
            this.resetHiddenRows();
            this.addRegex(this.getSearchValue(), null, true);
            this.applyFilters();
        },
        
        addRegex : function(pattern)
        {
            pattern.split(new RegExp("[" + this.getDelimiter() + "]+", "gi")).forEach(function(regex){
                regex = qx.lang.String.trim(regex);
                if (regex.length > 0) {
                    this.Filters.push(["regex", regex]);
                }
            }, this);
        },
        
        applyFilters : function()
        {
            var rowCount    = this.getRowCount();
            var columnCount = this.getColumnCount();
            
            var filtersMatchedCount, filtersMatched, compareValue;
            for (var row = 0; row < rowCount; row++)
            {
                filtersMatched = [];
                filtersMatchedCount = 0;
                for (var col = 0; col < columnCount; col++)
                {
                    compareValue = this.getValue(col, row);
                    for (var i = 0; i < this.Filters.length; i++)
                    {
                        if (filtersMatched[i] !== true
                        &&  this.Filters[i][0] === "regex"
                        &&  (filtersMatched[i] = new RegExp(this.Filters[i][1], "gi").test(compareValue))
                        ) {
                            filtersMatchedCount++;
                        }
                    }
                }
                
                if (filtersMatchedCount < this.Filters.length)
                {
                    this.hideRows(row, 1, false);
                    row--;
                    rowCount--;
                }
            }
            
            var data =
            {
                firstRow    : 0,
                lastRow     : rowCount - 1,
                firstColumn : 0,
                lastColumn  : columnCount - 1
            };

            this.fireDataEvent("dataChanged", data);
        },

        resetHiddenRows : function()
        {
            var sortColumnIndex     = this.__sortColumnIndex;
            var sortColumnAscending = this.__sortAscending;

            this.base(arguments);

            this.sortByColumn(sortColumnIndex, sortColumnAscending);
        },

        removeRows : function(startIndex, howMany)
        {
            this.base(arguments, startIndex, howMany, false);
        },

        reloadData : qx.lang.Function.empty
    }
});