qx.Class.define("frontend.lib.ui.form.ComboTableOld",
{
    extend : combotable.ComboTable,

    include : [
        frontend.lib.ui.form.MSourceProperty
    ],

    construct : function()
    {
        this.base(arguments, new combotable.SearchableModel);
        this.addListener("changeModel", this._onChangeModel, this);
    },

    members :
    {
        getData : function()
        {
            return this.getSourceData();
        },

        setData : function( data )
        {

        },

        _applySource : function(source, old)
        {
            if (old) {
                old.removeListener("changeData", this._updateData, this);
                old.removeListener("changeDataKey", this._onChangeDataKey, this);
            }

            if (source) {
                source.addListener("changeData", this._updateData, this);
                source.addListener("changeDataKey", this._onChangeDataKey, this);
                this.__tableModel.setColumns(["ID", "Data"], ["id", source.getDataKey()]);
                this._updateData();
            }
        },

        _updateData : function()
        {
            var data = this.getSourceData();
            if (data) {
                this.__tableModel.setDataAsMapArray(data);
            }
        },

        _onChangeDataKey : function(e)
        {
            this.__tableModel.setColumns(["ID", "Data"], ["id", e.getData()]);
        },

        _onChangeModel : function(e)
        {
            var value = e.getData();
            var old   = e.getOldData();
            if (value != old)
            {
                qx.event.Timer.once(function(){
                    var tm = this.__tableModel;
                    var table = this.__table;
                    var sm = table.getSelectionModel();
                    var data = tm.getData();
                    for (var i = 0, len = data.length; i < len; i++) {
                        if (value == tm.getValue(0, i)) {
                            sm.setSelectionInterval(i, i);
                            table.setFocusedCell(1, i, true);
                            this.setValue(tm.getValue(1, i));
                            this.setValid(true);
                            break;
                        }
                    }
                }, this, 500);
            }
        },

        resetValue : function()
        {
            this.setValue(null);
            this.setModel(null);

            try {
                this.__highlighter.setSearchString(null);
                this.__tableModel.setSearchString(null);
            } catch (ex) {}
        },

        // SIM overriden
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id) {
                case "list":
                    control = this._makeTable();
                    break;
            }

            return control || this.base(arguments, id);
        },

        // SIM overriden
        _makeTable : function()
        {
            var tm = this.__tableModel;

            var custom = {
                tableColumnModel : function(obj) {
                    return new qx.ui.table.columnmodel.Resize(obj);
                },

                tablePaneHeader : function(obj) {
                    return new combotable.NoHeader(obj);
                },

                initiallyHiddenColumns : [ 0 ]
            };

            var container = new qx.ui.container.Composite(new qx.ui.layout.Canvas).set({
                height     : this.getMaxListHeight(),
                allowGrowX : true,
                allowGrowY : true
            });

            container.add(new qx.ui.basic.Label(this.tr("Filtering ...")).set({
                padding    : [ 3, 3, 3, 3 ],
                allowGrowX : true,
                allowGrowY : true,
                enabled    : false
            }));

            var table = this.__table = new qx.ui.table.Table(tm, custom).set({
                focusable         : false,
                keepFocus         : true,
                height            : null,
                width             : null,
                allowGrowX        : true,
                allowGrowY        : true,
                decorator         : null,
                alwaysUpdateCells : true
            });

            var textfield = this.getChildControl("textfield");

            textfield.addListenerOnce("input", function(e) {
                tm.addListener("dataChanged", this._onTableDataChanged, this);
            }, this);

            var armClick = function() {
                textfield.addListenerOnce("execute", function(e) {
                    if (! textfield.hasState("selected")) {
                        textfield.selectAllText();
                    }
                });
            };

            armClick();
            textfield.addListener("focusout", armClick, this);

            table.getDataRowRenderer().setHighlightFocusRow(true);

            table.set({
                showCellFocusIndicator        : false,
                headerCellsVisible            : false,
                columnVisibilityButtonVisible : false,
                focusCellOnMouseMove          : true
            });

            var tcm = table.getTableColumnModel();

            this.__highlighter = new frontend.lib.ui.form.combotable.CellHighlighter();
            tcm.setDataCellRenderer(1, this.__highlighter);
            container.add(table, { edge : 0 });
            return container;
        }
    }
});