qx.Class.define("frontend.lib.ui.form.ComboTable",
{
    extend : qx.ui.form.ComboBox,

    implement : [
        frontend.lib.ui.ISearchBox
    ],

    include : [
        qx.ui.form.MModelProperty
    ],

    properties :
    {
        loading :
        {
            check : "Boolean",
            init : false,
            apply : "_applyLoading"
        },

        dataColumn :
        {
            check : "String || Function",
            apply : "_applyDataColumn"
        },

        dataUrl :
        {
            check : "String",
            apply : "_applyDataUrl"
        },

        selectOnlyOption :
        {
            check : "Boolean",
            init : false
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setValue(null);
        this.setModel(null);
        this.setPlaceholder("wpisz frazę do wyszukania...");

        this._getLayout().setSpacing(3);

        this.__tableModel = new frontend.lib.ui.table.model.Remote();
        this.__tableModel.addListener("loadedRowCount", this._onTableModelLoadedRowCount, this);

        this.__highlighter = new frontend.lib.ui.table.cellrenderer.Highlight();

        var textfield   = this.getChildControl("textfield");
        var clearButton = this.getChildControl("clear-button");
        
        this._addAfter(clearButton, textfield);

        textfield.bind("input", this.__highlighter, "highlightText");
        textfield.bind("value", clearButton, "visibility", {
            converter : function(value) {
                return !!value ? "visible" : "hidden";
            }
        });
        
        this.addListener("changeModel", this._onChangeModel, this);
    },

    members :
    {
        __tableModel : null,

        __table : null,

        __highlighter : null,

        getTextField : function()
        {
            return this.getChildControl("textfield");
        },

        _applyLoading : function(value, old)
        {
            this.__table.setVisibility(value ? "hidden" : "visible");
            qx.ui.core.queue.Visibility.flush();
            qx.html.Element.flush();
        },

        _applyDataColumn : function(value, old)
        {
            if (typeof value == "function") {
                this.__tableModel.setDataFormatter("data_column", value);
                value = "data_column";
            }
            this.__tableModel.setColumns(
                ["id", value],
                ["id", value]
            );
        },

        _applyDataUrl : function(value, old)
        {
            this.__tableModel.setDataUrl(value);
        },

        _onTableDataChanged : function(e)
        {
            this.setLoading(false);
            var rowCount = this.__tableModel.getRowCount();
            if (rowCount) {
                var value = this.getValue();
                if (value) {
                    this._select(null, value, 0);
                }
            } else if (this.isRequired()) {
                this.setValid(false);
            }

            if (!this.getChildControl("popup").isVisible()) {
                var rowData = this.getSelectedRowData();
                if (rowData) {
                    this._select(rowData.key, rowData.value, rowData.rowIndex);
                } else {
                    this.setModel(null);
                }
            }
        },

        _select : function(id, value, rowIndex)
        {
            this.setValue(value);
            this.setModel(id);
            this.setValid(true);
//            this.__table.setFocusedCell(1, rowIndex, true);
//            this.__table.getSelectionModel().setSelectionInterval(rowIndex, rowIndex);
        },

        _onChangeModel : function(e)
        {
            var id    = e.getData();
            var value = this.getValue();
            
            if (id != null && value == null) {
                var request = new frontend.lib.io.HttpRequest().set({
                    url    : Urls.resolve(this.getDataUrl(), id),
                    method : "GET"
                });
                request.addListenerOnce("success", function(e){
                    var rowData = request.getResponseJson();
                    if (rowData) {
                        var value;
                        var dataColumn = this.getDataColumn();
                        if (typeof dataColumn == "function") {
                            value = dataColumn.call(this, rowData);
                        } else {
                            value = rowData[dataColumn];
                        }
                        this._select(id, value, 0);
                    } else {
                        this.setModel(null);
                    }
                }, this);
                request.send();
            }
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch(id)
            {
                case "textfield":
                    control = this.base(arguments, id);
                    control.setLiveUpdate(true);
                    control.addListener("input", this._onTextFieldInput, this);
                    break;

                case "list":
                    control = this._makeTable();
                    break;

                case "clear-button":
                    control = new qx.ui.basic.Image("icon/16/actions/edit-delete.png").set({
                        toolTipText : "wyczyść pole wyszukiwania",
                        cursor : "pointer"
                    });
                    control.addListener("click", this.clearTextField, this);
                    break;
            }
            return control || this.base(arguments, id);
        },

        _onTextFieldInput : function(e)
        {
            this.__table.getSelectionModel().resetSelection();
            this.__table.setFocusedCell(null, null, true);
            this.setLoading(true);
            this.open();
            this.fireDataEvent("input", e.getData(), e.getOldData());
        },

        _onTextFieldChangeValue : function(e)
        {
            this.fireDataEvent("changeValue", e.getData(), e.getOldData());
        },

        _makeTable : function()
        {
            var custom = {
                tableColumnModel : function(obj)
                {
                    return new qx.ui.table.columnmodel.Resize(obj);
                },

                initiallyHiddenColumns : [ 0 ]
            };

            var container = new qx.ui.container.Composite(new qx.ui.layout.Canvas).set({
                height     : this.getMaxListHeight(),
                allowGrowX : true,
                allowGrowY : true
            });

            container.add(new qx.ui.basic.Label(this.tr("Filtering ...")).set({
                padding    : 3,
                allowGrowX : true,
                allowGrowY : true,
                enabled    : false
            }));

            var table = this.__table = new frontend.lib.ui.table.Table(this.__tableModel, custom).set({
                focusable         : false,
                keepFocus         : true,
                height            : null,
                width             : null,
                allowGrowX        : true,
                allowGrowY        : true,
                decorator         : null,
                alwaysUpdateCells : true
            });

            table.getDataRowRenderer().setHighlightFocusRow(true);

            this.getTextField().addListenerOnce("input", function(e){
                this.__tableModel.connectToSearchBox(this);
                this.__tableModel.addListener("dataChanged", this._onTableDataChanged, this);
            }, this);

            table.set({
                showCellFocusIndicator        : false,
                headerCellsVisible            : false,
                columnVisibilityButtonVisible : false,
                focusCellOnMouseMove          : true
            });

            table.getTableColumnModel().setDataCellRenderer(1, this.__highlighter);
            container.add(table, {edge : 0});
            return container;
        },

        _onKeyPress : function(e)
        {
            var popup         = this.getChildControl("popup");
            var loading       = this.getLoading();
            var keyIdentifier = e.getKeyIdentifier();

            switch (keyIdentifier)
            {
                case "Down":
                case "Up":
                    if (loading) {
                        e.stop();
                        e.stopPropagation();
                        return;
                    }
                    if (!popup.isVisible()) {
                        this.open();
                    }
                    this["row" + keyIdentifier]();
                    e.stop();
                    e.stopPropagation();
                    break;

                case "Enter":
                case "Escape":
                case "Tab":
                    if (loading) {
                        e.stop();
                        e.stopPropagation();
                        return;
                    }
                    if (popup.isVisible()) {
                        e.stop();
                        e.stopPropagation();
                        this.close();
                    }
                    break;
            }
        },

        _onClick : function(e)
        {
            if (e.getTarget() == this.getChildControl("button")) {
                this.open();
            }
        },

        _onListChangeSelection : function(e)
        {

        },

        _onPopupChangeVisibility : function(e)
        {
            var visibility = e.getData();
            var old = e.getOldData();
            if (visibility == "hidden") {
//                this.getChildControl("button").removeState("selected");
                var rowData = this.getSelectedRowData();
                if (rowData) {
                    this._select(rowData.key, rowData.value, rowData.rowIndex);
                }
//                else if (old != "excluded" && (this.getValue() || this.getRequired())) {
//                    this.setModel(null);
//                    this.setValid(false);
//                }
            } else {
//                this.getChildControl("button").addState("selected");
            }
        },

        resetValue : function()
        {
            this.setValue(null);
            this.setModel(null);

            this.__tableModel.reloadData();
        },

        rowDown : function()
        {
            var rowData  = this.getSelectedRowData();
            var rowIndex = 0;
            if (rowData) {
                rowIndex = Math.min(rowData.rowIndex + 1, this.__tableModel.getRowCount() - 1);
            }

            this.__table.setFocusedCell(1, rowIndex, true);
            this.__table.getSelectionModel().setSelectionInterval(rowIndex, rowIndex);
        },

        rowUp : function()
        {
            var rowData  = this.getSelectedRowData();
            var rowIndex = 0;

            if (rowData) {
                rowIndex = Math.max(rowData.rowIndex - 1, 0);
            }
            this.__table.setFocusedCell(1, rowIndex, true);
            this.__table.getSelectionModel().setSelectionInterval(rowIndex, rowIndex);
        },

        getSelectedRowData : function()
        {
            var rowData = this.__table.getSelectedRowData();
            if (rowData) {
                return {
                    rowIndex : rowData.rowIndex,
                    key      : rowData[this.__tableModel.getColumnId(0)],
                    value    : rowData[this.__tableModel.getColumnId(1)]
                }
            }
            return null;
        },

        _onTableModelLoadedRowCount : function(e)
        {
            if (!this.getSelectOnlyOption()) {
                return;
            }
            
            var tableModel = this.__tableModel;
            var rowCount = tableModel.getRowCount();
            if (rowCount == 1) {
                tableModel.addListenerOnce("loadedRowData", function(e){
                    var rowData = tableModel.getRowData(0);
                    if (rowData != null) {
                        var dataColumn = this.getDataColumn(), value;
                        if (typeof dataColumn == "function") {
                            value = rowData["data_column"];
                        } else {
                            value = rowData[dataColumn];
                        }
                        this._select(rowData.id, value, 0);
                    }
                }, this);
                tableModel.prefetchRows(0, 0);
            }
        },

        clearTextField : function()
        {
            this.resetValue();
        },

        getClearSearchBoxOnConnect : function()
        {
            return true;
        }
    }
});