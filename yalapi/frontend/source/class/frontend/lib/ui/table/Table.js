qx.Class.define("frontend.lib.ui.table.Table",
{
    extend : qx.ui.table.Table,

    events :
    {
        "changeSelectedCount" : "qx.event.type.Data"
    },

    properties :
    {
        newTableColumnModel :
        {
            refine : true,
            init : function(obj)
            {
                return new qx.ui.table.columnmodel.Resize(obj);
            }
        },

        newTablePaneScroller :
        {
            refine : true,
            init : function(obj)
            {
                return new frontend.lib.ui.table.pane.Scroller(obj);
            }
        }
    },

    construct : function(tableModel, custom)
    {
        this.base(arguments, tableModel, custom);
        this.setShowCellFocusIndicator(false);

        var tableSelectionModel = this.getSelectionModel();
        tableSelectionModel.setSelectionMode(qx.ui.table.selection.Model.SINGLE_SELECTION);
        tableSelectionModel.addListener("changeSelection", this._onChangeSelection, this);
    },
    
    members :
    {
        // SIM overriden
        _updateStatusBar : function()
        {
            if (this.getStatusBarVisible())
            {
                var rowCount = this.getTableModel().getRowCount();
                var text;

                if (rowCount > 0) {
                    if (rowCount == 1) {
                        text = this.tr("1 row");
                    } else {
                        text = this.tr("%1 rows", rowCount);
                    }
                } else {
                    text = this.tr("0 rows");
                }

                if (this.__additionalStatusBarText) {
                    if (text) {
                        text += this.__additionalStatusBarText;
                    } else {
                        text = this.__additionalStatusBarText;
                    }
                }

                if (text) {
                    this.getChildControl("statusbar").setValue(text);
                }
            }
        },

        _onChangeSelection : function(e)
        {
            var selectedCount = this.getSelectionModel().getSelectedCount();
            this.fireDataEvent("changeSelectedCount", selectedCount);
        },

        getSelectedRowData : function()
        {
            var selectionRanges = this.getSelectionModel().getSelectedRanges();
            for (var selectionIndex = 0, selectionCount = selectionRanges.length; selectionIndex < selectionCount; selectionIndex++) {
                var interval = selectionRanges[selectionIndex];
                for (var rowIndex = interval.minIndex; rowIndex <= interval.maxIndex; rowIndex++) {
                    var rowData = this.getTableModel().getRowData(rowIndex);
                    if (rowData) {
                        rowData.rowIndex = rowIndex;
                        return rowData;
                    }
                }
            }
            return null;
        }
    }
});