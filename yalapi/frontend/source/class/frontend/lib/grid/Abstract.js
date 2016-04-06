qx.Class.define("frontend.lib.grid.Abstract",
{
    extend : qx.ui.container.Composite,

    type : "abstract",

    include : [
        frontend.MMessage,
        frontend.lib.util.MGetSource
    ],

    construct : function()
    {
        this.base(arguments);
        
        this.setLayout(new qx.ui.layout.VBox);

        this._createTableModel();
        this._createChildControl("toolbar");
        this._createChildControl("table");

        var toolbar = this.getChildControl("toolbar");

        toolbar.getChildControl("add-button").addListener("execute", this._onClickButtonAdd, this);
        toolbar.getChildControl("delete-selected-button").addListener("execute", this._onClickButtonDeleteMarked, this);
        toolbar.getChildControl("refresh-button").addListener("execute", this._onClickButtonRefresh, this);

        this._tableModel.connectToSearchBox(toolbar.getChildControl("searchbox"));
    }, 

    members :
    {
        _data               : null,

        _table              : null,
        _tableModelUrl      : null,
        _tableModelUrlParams: null,
        _tableModel         : null,

        _tableKeys          : null,
        _tableColumnNames   : null,

        _idColumnNumber     : null,
        _dataFormatters     : {},

        editFormClass       : null,
        addFormClass        : null,

        getModel : function()
        {
            return this.getChildControl("table").getTableModel();
        },

        getTable : function()
        {
            return this._table;
        },

        _tableCustom : null,

        _createTable : function()
        {
            if (this._table == null)
            {
                this._table = new frontend.lib.ui.table.Grid(this._tableModel, this._tableCustom);

                this._table.setMarginTop(15);
                this._table.addListener('changeRowSelectedCount', this._onRowSelectOrAppear, this);
                this._table.addListener('appear', this._onRowSelectOrAppear, this);

                this._table.addListener("editRowClick", this._onEditRowClick, this);
                this._table.addListener("deleteRowClick", this._onDeleteRowClick, this);

                this._table.bind("showCheckboxes", this.getChildControl("toolbar").getChildControl("delete-selected-button"), "visibility", {
                    converter : function(value) {
                        return value ? "visible" : "excluded"
                    }
                });

                this.add(this._table, { flex: 1 });
            }

            return this._table;
        },

        _createTableModel : function()
        {
            this._tableModel = new frontend.lib.ui.table.model.Remote().set({
                dataUrl : Urls.resolve(this._tableModelUrl, this._tableModelUrlParams)
            });

            this._tableModel.setColumns(
                this._tableColumnNames,
                this._tableKeys
            );

            if (this._dataFormatters) {
                for (var i in this._dataFormatters) {
                    this._tableModel.setDataFormatter(i, this._dataFormatters[i]);
                }
            }
        },


        _createToolbar : function()
        {
            var toolbar = new frontend.lib.ui.Toolbar();
            this.add( toolbar );
            return toolbar;
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "toolbar" :
                    control = this._createToolbar();
                break;

                case "table" :
                    control = this._createTable();
                break;
            }

            return control || this.base(arguments, id);
        },

        _onDeleteRowClick : function(e)
        {
            var dialog = new frontend.lib.dialog.Confirm(Tools['tr']('lib.grid.Abstract.delete-record-confirm')),
                rowData = e.getData();

            dialog.addListener('yes', function() {
                var tableModel = this.getChildControl("table").getTableModel(),
                    request = new frontend.lib.io.HttpRequest(Urls.resolve(this._tableModelUrl, rowData.id), 'DELETE');
                request.addListener("success", tableModel.reloadData, tableModel);
                request.send();
            }, this);
        },

        _onEditRowClick : function(e)
        {
            var data = e.getData();
            var tableModel = this.getChildControl("table").getTableModel();

            var form = new this.editFormClass(data);
            form.getForm().addListener("completed", tableModel.reloadData, tableModel);
            form.center();
            form.open();
        },

        _onClickButtonAdd : function()
        {
            var recordForm = new this.addFormClass();
            recordForm.getForm().addListener("completed", this._tableModel.reloadData, this._tableModel);
            recordForm.center();
            recordForm.open();
        },

        _onClickButtonRefresh : function()
        {
            this._tableModel.reloadData();
        },

        _onRowSelectOrAppear: function()
        {
            this.getChildControl('toolbar')
                .getChildControl('delete-selected-button')
                .setEnabled(this._table.getSelectedRowCount() > 0);
        },

        _onClickButtonDeleteMarked : function(row)
        {
            var selected = this._table.getSelectedRows(),
                length = selected.length,
                confirm = new frontend.lib.dialog.Confirm(Tools.tr('lib.grid.Abstract.delete-records-confirm'));
            
            confirm.addListener('yes', function() {
                for (var id in selected) {
                    var request = new frontend.lib.io.HttpRequest(Urls.resolve(this._tableModelUrl, id), "DELETE");
                    request.send();
                }
                this._tableModel.reloadData();
                this._table.resetSelectedRows();
            }, this);
        },
        
        _createChildControl : function(id)
        {
            if (!this.__childControls) {
                this.__childControls = {};
            } else if (this.__childControls[id]) {
                throw new Error("Child control '" + id + "' already created!");
            }

            var control = this._createChildControlImpl(id);

            if (!control) {
                throw new Error("Unsupported control: " + id);
            }

            this.fireDataEvent("createChildControl", control);

            return this.__childControls[id] = control;
        }
    }
});