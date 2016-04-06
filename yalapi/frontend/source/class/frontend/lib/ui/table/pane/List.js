qx.Class.define("frontend.lib.ui.table.pane.List",
{
    extend : qx.ui.table.pane.Pane,

    members :
    {
        __divHtml : null,
        
        updateContent : function(completeUpdate, scrollOffset, onlyRow, onlySelectionOrFocusChanged)
        {
            if (completeUpdate) {
                this.__rowCacheClear();
            }

            if (scrollOffset && Math.abs(scrollOffset) <= Math.min(10, this.getVisibleRowCount())) {
                this._scrollContent(scrollOffset);
            } else if (onlySelectionOrFocusChanged && !this.getTable().getAlwaysUpdateCells()) {
                this._updateRowStyles(onlyRow);
            } else {
                this._updateAllRows();
            }
        },

        _updateAllRows : function()
        {
            var elem = this.getContentElement().getDomElement();
            if (!elem) {
                // pane has not yet been rendered
                this.addListenerOnce("appear", arguments.callee, this);
                return;
            }

            if (this.__divHtml === null) {
                elem.innerHTML = "<div style='overflow:hidden;width:100%;'></div>";
                this.__divHtml = elem.firstChild;
            }

            var table = this.getTable();

            var tableModel = table.getTableModel();
            var paneModel  = this.getPaneScroller().getTablePaneModel();

            var colCount = paneModel.getColumnCount();
            var firstRow = this.getFirstVisibleRow();

            var rowCount      = this.getVisibleRowCount();
            var modelRowCount = tableModel.getRowCount();

            if (firstRow + rowCount > modelRowCount) {
                rowCount = Math.max(0, modelRowCount - firstRow);
            }

            this._getRowsHtml(firstRow, rowCount);

            var rowWidth = paneModel.getTotalWidth();
            this.setWidth(rowWidth);

            this.__lastColCount = colCount;
            this.__lastRowCount = rowCount;
            
            this.fireEvent("paneUpdated");
        },

        _scrollContent : function(rowOffset)
        {
            var el = this.getContentElement().getDomElement();
            if (!(el && el.firstChild)) {
                this._updateAllRows();
                return;
            }
            
            var rowCount = this.getVisibleRowCount();
            var firstRow = this.getFirstVisibleRow();

            var tabelModel    = this.getTable().getTableModel();
            var modelRowCount = 0;

            modelRowCount = tabelModel.getRowCount();

            if (firstRow + rowCount > modelRowCount) {
                this._updateAllRows();
                return;
            }

            this._getRowsHtml(firstRow, rowCount);

            // update focus indicator
            if (this.__focusedRow !== null)
            {
                this._updateRowStyles(this.__focusedRow - rowOffset);
                this._updateRowStyles(this.__focusedRow);
            }
            this.fireEvent("paneUpdated");
        },

        // SIM @TODO maybe some cacheing ?
        _getRowsHtml : function(firstRow, rowCount)
        {
            var table          = this.getTable();
            var selectionModel = table.getSelectionModel();
            var tableModel     = table.getTableModel();
            var columnModel    = table.getTableColumnModel();
            var paneModel      = this.getPaneScroller().getTablePaneModel();
            var rowRenderer    = table.getDataRowRenderer();

            tableModel.prefetchRows(firstRow, firstRow + rowCount - 1);

            var rowHeight = table.getRowHeight();
            var colCount  = paneModel.getColumnCount();
            var left      = 0;
            var cols      = [];

            // precompute column properties
            for (var x = 0; x < colCount; x++)
            {
                var col       = paneModel.getColumnAtX(x);
                var cellWidth = columnModel.getColumnWidth(col);
                cols.push({
                    col: col,
                    xPos: x,
                    editable: tableModel.isColumnEditable(col),
                    focusedCol: this.__focusedCol == col,
                    styleLeft: left,
                    styleWidth: cellWidth
                });

                left += cellWidth;
            }

            var paneReloadsData = false;
            var cellrenderer = new frontend.lib.ui.table.cellrenderer.ListCell();

            qx.bom.Element.empty(this.__divHtml);

            for (var row = firstRow; row < firstRow + rowCount; row++)
            {
                var selected = selectionModel.isSelectedIndex(row);
                var focusedRow = (this.__focusedRow == row);

                var cellInfo =
                {
                    table       : table,
                    styleHeight : rowHeight,
                    row         : row,
                    selected    : selected,
                    focusedRow  : focusedRow,
                    rowData     : tableModel.getRowData(row),
                    cols        : cols
                };

                if (!cellInfo.rowData) {
                    paneReloadsData = true;
                }

                var rowStyle = rowRenderer.createRowStyle(cellInfo) + ";position:relative;height:" + rowHeight+ "px;width:100%;";
//                var rowAttributes = rowRenderer.getRowAttributes(cellInfo);
//                var rowClass = rowRenderer.getRowClass(cellInfo);

                var div = qx.bom.Element.create("div", {
                    style : rowStyle
                });

                this.__divHtml.appendChild(div);

//                var rowWidth = this.getPaneScroller().getTablePaneModel().getTotalWidth();

                // SIM this is the only way I could think of
                // to renderer row in container with layout
                try {
                    var container = new qx.ui.root.Inline(div, true, false).set({
                        appearance : "widget",
                        height     : rowHeight - 10,
                        padding    : [10, 20, 10, 10]
                    });
                    cellrenderer.setContainer(container);
                    cellrenderer.createDataCellHtml(cellInfo);
                } catch (ex) {
                    
                }
            }
            this.fireDataEvent("paneReloadsData", paneReloadsData);
        }
    }
});