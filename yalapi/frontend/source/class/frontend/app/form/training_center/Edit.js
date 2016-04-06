/**
 *  #asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)
 */

qx.Class.define("frontend.app.form.training_center.Edit",
{
    extend : frontend.app.form.training_center.Add,

    include : [
        frontend.MMessage
    ],

    construct : function(rowData)
    {
        delete rowData.city_zip_code;
        if (rowData.description == null) {
            rowData.description = "";
        }
        this._TCData = rowData;
        this.base(arguments);
        this.populate();
    },

    members :
    {
        _TCData : null,

        _createGridRooms : function()
        {
            this._controls['gridRooms'] = new frontend.app.grid.rooms.Edit(this._TCData.id);
            this._controls['gridRooms'].set({ maxHeight: 250, margin : 0, padding : 0 });

            this._controls['gridRooms'].getChildControl('table').getTableModel().setDataUrl(Urls.resolve('ROOMS', {
                "pager[training_center_id]" : this._TCData.id
            }));
            this._controls['gridRooms'].getChildControl('table').getTableModel().reloadData();

            return this._controls['gridRooms'];
        },

        populate : function()
        {
            this._getWebAdress();
            this._populateTrainingCenterForm();
            this._getResources();
        },

        _getWebAdress : function()
        {
            var request = new frontend.lib.io.HttpRequest(Urls.resolve("TRAINING_CENTERS"), "GET");
            request.addListener("success", function(data){
                data = qx.lang.Json.parse(data.getTarget().getResponse());
                this._TCData.url = data.url;
            }, this);
            request.setRequestData({ 'id' : this._TCData.id });
            request.send();
        },

        _getResources : function()
        {
            var request = new frontend.lib.io.HttpRequest(Urls.resolve("RESOURCES"), "GET");
            request.addListener("success", this._populateResourcesForm, this);
            request.setRequestData({ 'training_center_id' : this._TCData.id });
            request.send();
        },

        _populateResourcesForm : function(data)
        {
            data = qx.lang.Json.parse(data.getTarget().getResponse());
            if(data.length > 0)
            {
                var i = 0, length = data.length;
                for( i; i < length; i++ )
                {
                    this._addRowToPage();
                    this._resources[i].resourceRow.name.setModel(data[i].resource_type_id);
                    this._resources[i].resourceRow.quantity.setValue(data[i].amount.toString());
                }
            }
        },

        _populateTrainingCenterForm : function()
        {
            this._controls['formGeneral'].populate(this._TCData);
            this._controls['formDescription'].populate(this._TCData);
        },

        _saveData : function()
        {
            var data = {};
            data.training_center = qx.lang.Object.merge(
                this._controls['formGeneral'].getValues(),
                this._controls['formDescription'].getValues()
            );
            data.resources = [];

            var length = this._resources.length;
            for(var i = 0; i < length; i++)
            {
                if(typeof this._resources[i] !== "undefined")
                {
                    data.resources[i] = {
                        type : this._resources[i].resourceRow.name.getModel(),
                        quantity : this._resources[i].resourceRow.quantity.getValue()
                    };
                }
            }

            var request = new frontend.lib.io.HttpRequest(Urls.resolve("TRAINING_CENTERS", this._TCData.id), "PUT");
            request.setRequestData({data:qx.lang.Json.stringify(data)});
            request.addListener("success", function(e){
                new frontend.lib.dialog.Message(Tools.tr("form.training_center.add:edited"));
                this.close();
                this.fireEvent("completed");
            }, this);
            request.send();
        }
    }
});