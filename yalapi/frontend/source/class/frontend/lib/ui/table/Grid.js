qx.Class.define("frontend.lib.ui.table.Grid",
{
    extend : frontend.lib.ui.table.Table,

    include : [
        frontend.lib.ui.table.MTable
    ],

    events :
    {
        "changeShowCheckboxes"   : "qx.event.type.Data",
        "changeShowEditRemove"   : "qx.event.type.Data",
        "changeRowSelectedCount" : "qx.event.type.Data",
        "changeRowSelected"      : "qx.event.type.Data"
    },

    properties :
    {
        columnVisibilityButtonVisible :
        {
            refine : true,
            init : false
        },

        showCheckboxes :
        {
            check : "Boolean",
            init : false,
            event : "changeShowCheckboxes",
            apply : "_applyShowCheckboxes"
        },

        checkRowOnDblClick :
        {
            check : "Boolean",
            init : true
        },
        
        showEditRemove :
        {
            check : "Boolean",
            init : false,
            event : "changeShowEditRemove",
            apply : "_applyShowEditRemove"
        },

        autoReloadDataOnAppear :
        {
            check : "Boolean",
            init : true
        }
    },

    construct : function(tableModel, custom)
    {
        this.base(arguments, tableModel, custom);

        this.addListener("changeRowSelected", this._onChangeRowSelected, this);
        this.addListener("changeRowSelectedCount", this._updateStatusBar, this);

        this.addListener("cellClick", this._onCellClick(false), this);
        this.addListener("cellDblclick", this._onCellClick(true), this);

        this.addListener("changeRowSelectedCount", this._onPaneUpdated, this);
        this.getPaneScroller(0).getTablePane().addListener("paneUpdated", this._onPaneUpdated, this);

        this.addListener("headerCheckboxClick", this._onHeaderCheckboxClick, this);

        this.addListenerOnce("appear", function(e){
            this.addListener("appear", this._onTableAppear, this);
        }, this);

        this.addListener("disappear", this._onTableDisappear, this);

        this.resetSelectedRows();
    },
    
    members :
    {
        __selectedRows : {},

        __selectedRowsCount : 0,

        _onTableAppear : function(e)
        {
            if (this.getAutoReloadDataOnAppear()) {
                this.getTableModel().reloadData();
                if (this.__lastVisibleRowCount != null) {
                    qx.event.Timer.once(function(){
                        this.getPaneScroller(0).getTablePane().setVisibleRowCount(this.__lastVisibleRowCount);
                    }, this, 100);
                }
            }
        },

        __lastVisibleRowCount : null,

        _onTableDisappear : function(e)
        {
            this.__lastVisibleRowCount = this.getPaneScroller(0).getTablePane().getVisibleRowCount();
            if (this.getAutoReloadDataOnAppear()) {
                this.getPaneScroller(0).getTablePane().setVisibleRowCount(0);
                this.getSelectionModel().resetSelection();
            }
        },

        _onPaneUpdated : function(e)
        {
            var checkbox;
            try {
                checkbox = this.getTableColumnModel().getHeaderCellRenderer(0).checkbox;
            } catch (ex) {

            }
            if (checkbox)
            {
                var tableModel = this.getTableModel();
                var tablePane  = this.getPaneScroller(0).getTablePane();

                var firstVisibleRow = tablePane.getFirstVisibleRow();
                var rowCount        = Math.min(tablePane.getVisibleRowCount(), tableModel.getRowCount());
                
                var visibleSelectedRowsCount = 0;

                for (var row = firstVisibleRow; row < firstVisibleRow + rowCount; row++) {
                    var rowData = tableModel.getRowData(row);
                    if (rowData && this.isSelectedRow(rowData.id)) {
                        visibleSelectedRowsCount++;
                    }
                }
//                console.log(visibleSelectedRowsCount, "visibleSelectedRowsCount")
                if (rowCount == 0 || visibleSelectedRowsCount == 0) {
                    checkbox.setValue(false);
                } else if (visibleSelectedRowsCount == rowCount) {
                    checkbox.setValue(true);
                } else {
                    checkbox.setValue(null);
                }
            }
        },

        _onHeaderCheckboxClick : function(e)
        {
            var checkbox;
            try {
                checkbox = this.getTableColumnModel().getHeaderCellRenderer(0).checkbox;
            } catch (ex) {
                
            }
            if (checkbox)
            {
                var tableModel = this.getTableModel();
                var tablePane  = this.getPaneScroller(0).getTablePane();

                var firstVisibleRow = tablePane.getFirstVisibleRow();
                var rowCount        = Math.min(tablePane.getVisibleRowCount(), tableModel.getRowCount());

                var selected = checkbox.getValue() !== true;
                for (var rowIndex = firstVisibleRow; rowIndex < firstVisibleRow + rowCount; rowIndex++)
                {
                    var rowData = tableModel.getRowData(rowIndex);
                    if (rowData) {
                        this.fireDataEvent("changeRowSelected", {
                            selected : selected,
                            rowData  : rowData,
                            rowIndex : rowIndex,
                            noEvent  : true
                        });
                    }
                }
                // SIM fireing "dataChanged" forces table to redraw all visible rows
                // so there is no sense in doing it after each row's change
                // just fire it once after all rows are done
                tableModel.fireDataEvent("dataChanged", {
                    firstRow    : firstVisibleRow,
                    lastRow     : firstVisibleRow + rowCount,
                    firstColumn : 0,
                    lastColumn  : 0
                });
                this.fireDataEvent("changeRowSelectedCount", this.getSelectedRowCount());
            }
        },

        _onCellClick : function(dblclick)
        {
            return function(e)
            {
                var columnIndex = e.getColumn();
                var rowIndex    = e.getRow();
                var tableModel  = this.getTableModel();
                var rowData     = tableModel.getRowData(rowIndex);

                if (rowData)
                {
                    var action = e.getOriginalTarget().getAttribute("action");
                    if (action) {
                        this.fireDataEvent(action + "RowClick", rowData);
                    }

                    if (this.getShowCheckboxes()
                    && (
                        (dblclick && this.getCheckRowOnDblClick())
                        ||
                        (!dblclick && columnIndex == 0))
                    ) {
                        this.fireDataEvent("changeRowSelected", {
                            selected : !this.isSelectedRow(rowData.id),
                            rowData  : rowData,
                            rowIndex : rowIndex
                        });
                    }
                }
            }
        },

        _onChangeRowSelected : function(e)
        {
            var data     = e.getData();
            var selected = data.selected;
            var rowData  = data.rowData;

            rowData["selected_row"] = selected;

            if (selected) {
                this.__selectedRows[rowData.id] = rowData;
            } else {
                delete this.__selectedRows[rowData.id];
            }

            this.__selectedRowsCount = qx.lang.Object.getLength(this.__selectedRows);

            if (data.noEvent !== true)
            {
                var tableModel = this.getTableModel();
                tableModel.fireDataEvent("dataChanged", {
                    firstRow    : data.rowIndex,
                    lastRow     : data.rowIndex,
                    firstColumn : 0,
                    lastColumn  : 0
                });
                this.fireDataEvent("changeRowSelectedCount", this.getSelectedRowCount());
            }
        },

        isSelectedRow : function(id)
        {
            return this.__selectedRows[id] != null;
        },

        getSelectedRowCount : function()
        {
            return this.__selectedRowsCount;
        },

        getSelectedRows : function()
        {
            return this.__selectedRows;
        },

        getSelectedRowsIds : function()
        {
            return qx.lang.Object.getKeys(this.__selectedRows);
        },

        resetSelectedRows : function()
        {
            this.__selectedRows      = {};
            this.__selectedRowsCount = 0;
            this.fireDataEvent("changeRowSelectedCount", 0);
            
            return this;
        },

        // SIM overriden
        _updateStatusBar : function()
        {
            if (this.getStatusBarVisible())
            {
                var selectedRowCount = this.getSelectedRowCount();
                var rowCount         = this.getTableModel().getRowCount();
                var showCheckboxes   = this.getShowCheckboxes();
                var text;

                if (rowCount > 0) {
                    if (rowCount == 1) {
                        text = showCheckboxes
                             ? this.tr("1 row, %1 selected", selectedRowCount)
                             : this.tr("1 row");
                    } else {
                        text = showCheckboxes
                             ? this.tr("%1 rows, %2 selected", rowCount, selectedRowCount)
                             : this.tr("%1 rows", rowCount);
                    }
                } else {
                    text = showCheckboxes
                         ? this.tr("0 rows, %1 selected", selectedRowCount)
                         : this.tr("0 rows");
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

        // SIM overriden
        _applyTableModel : function(tableModel, old)
        {
            this.resetSelectedRows();

            var columnCount = tableModel.getColumnCount();

            if (columnCount > 0) {
                var columnIds   = ["selected_row", "extra_buttons"];
                var columnNames = [null, "edytuj / usuń"];
                for (var columnIndex = 0; columnIndex < columnCount; columnIndex++) {
                    columnNames.push(tableModel.getColumnName(columnIndex));
                    columnIds.push(tableModel.getColumnId(columnIndex));
                }
                tableModel.setColumns(columnNames, columnIds);
                tableModel.setDataFormatter("selected_row", function(rowData){
                    return this.getShowCheckboxes() ? this.isSelectedRow(rowData.id) : false;
                }, this);

                tableModel.setDataFormatter("extra_buttons", function(rowData){
                    return {
                        "edit"   : "button-edit",
                        "delete" : "button-delete"
                    }
                }, this);

                this.base(arguments, tableModel, old);
                
                var tableColumnModel = this.getTableColumnModel();
                tableColumnModel.setHeaderCellRenderer(0, new frontend.lib.ui.table.headerrenderer.Boolean);
                tableColumnModel.setDataCellRenderer(0, new frontend.lib.ui.table.cellrenderer.Boolean);
                tableColumnModel.setColumnVisible(0, this.getShowCheckboxes());
                tableColumnModel.setDataCellRenderer(1, new frontend.lib.ui.table.cellrenderer.Buttons);
                tableColumnModel.setColumnVisible(1, this.getShowEditRemove());
                this.addListenerOnce("appear", this._onFirstAppear, this);
                tableModel.sortByColumn(2, tableModel.isSortAscending());
                tableModel.setColumnSortable(0, false);
                tableModel.setColumnSortable(1, false);
            } else {
                this.base(arguments, tableModel, old);
            }
        },

        _onFirstAppear : function()
        {
            var tableColumnModel = this.getTableColumnModel();
            var behavior         = tableColumnModel.getBehavior();

            tableColumnModel.isColumnVisible(0) && behavior.setWidth(0, 50);
            tableColumnModel.isColumnVisible(1) && behavior.setWidth(1, 100);
        },
        
        _applyShowCheckboxes : function(value, old)
        {
            this.getTableColumnModel().setColumnVisible(0, value);
            this.getTableColumnModel().getHeaderCellRenderer(0).setCanEdit(value);
        },
        
        _applyShowEditRemove : function(value, old)
        {
            this.getTableColumnModel().setColumnVisible(1, value);
        }
    }
});