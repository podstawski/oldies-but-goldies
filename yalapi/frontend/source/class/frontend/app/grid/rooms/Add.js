qx.Class.define("frontend.app.grid.rooms.Add",
{
    extend : frontend.app.grid.rooms.Rooms,

    construct : function()
    {
        this.base(arguments);
        this.getTable().setShowEditRemove(true);
        this.setSimpleModel();
        this._tableModel.setDataFormatter("extra_buttons", function(rowData){
            return {
                "delete" : "button-delete"
            }
        });
        this._tableModel.__columnNameArr[1] = "usuń";
        this._tableModel.fireEvent("metaDataChanged");
    },

    include : [
        frontend.MMessage
    ],

    members :
    {
        setSimpleModel : function()
        {
            this._tableModel = new frontend.lib.ui.table.model.Simple();

            var oldModel = this.getChildControl("table").getTableModel(),
            columns = oldModel.getColumns(),
            keys = oldModel.getKeys();

            this._tableModel.setColumns(columns, keys);
            this.getChildControl("table").setTableModel(this._tableModel);
        },

        _onClickButtonDeleteMarked : function()
        {
            var selected = this._table.getSelectedRows(),
                length = selected.length, i = 0;

            for( i; i < length; i++) { this._tableModel.removeRows(selected[i].id, 1); }
        },

        _onClickButtonAdd : function()
        {
            var recordForm = new this.addFormClass();
            recordForm.getForm().setSubmitAfterValidation(false);
            recordForm.addListener("completed", function(e){
                var data = this.processDataObj(e.getData());
                data[0].id = this._lastId++;
                this._tableModel.addRows(data);
            }, this);
            recordForm.center();
            recordForm.open();
        },

        _onDeleteRowClick : function(e)
        {
            var rowData = e.getData();
            var tableModel = this.getChildControl("table").getTableModel();
            var modelData = tableModel.getData();

            for(var i in modelData) {
                if(modelData[i].id === rowData.id) {
                    modelData.splice(i, 1);
                    break;
                }
            }

            tableModel.setData(modelData);
        }
    }
});