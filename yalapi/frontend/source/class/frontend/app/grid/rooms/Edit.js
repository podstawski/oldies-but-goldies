qx.Class.define("frontend.app.grid.rooms.Edit",
{
    extend : frontend.app.grid.rooms.Rooms,

    construct : function(TCId)
    {
        this.base(arguments);

        this._TCId = TCId;
        this._table.setShowEditRemove(true);
        this._tableModel.setDataUrl(Urls.resolve('ROOMS') + "?training_center_id=" + this._TCId + "&search[training_center_id]=" + this._TCId);
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
        _onClickButtonAdd : function()
        {
            var form = new this.addFormClass();
            form.getForm().setSubmitAfterValidation(false);
            form.addListener("completed", function(e){
                var data = form.getForm().getValues();
                data['training_center_id'] = this._TCId;
                var request = new frontend.lib.io.HttpRequest(Urls.resolve('ROOMS'), 'POST' );
                request.setRequestData(data);
                request.addListener("success", function( e ) {
                    this.showMessage("Dodano nową salę");
                    this.getModel().reloadData();
                }, this );
                request.send();
            }, this);
            form.center();
            form.open();
        },

        _onEditRowClick : function(e)
        {
            var rowData = e.getData();
            var form = new this.editFormClass(rowData);
            form.getForm().setSubmitAfterValidation(false);
            form.addListener("completed", function(e){
                var data = form.getForm().getValues();
                data['training_center_id'] = this._TCId;
                var request = new frontend.lib.io.HttpRequest(Urls.resolve('ROOMS', rowData.id), 'PUT');
                request.setRequestData(data);
                request.addListener("success", function( e ) {
                    this.showMessage("Zapisano zmiany");
                    this.getModel().reloadData();
                }, this );
                request.send();
            }, this);
            form.center();
            form.open();
        }
    }
});