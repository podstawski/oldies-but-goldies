qx.Class.define("frontend.app.list.TrainingCenter",
{
    extend : frontend.lib.list.Abstract,

    construct : function()
    {
        this.base(arguments);

        var toolbar = this.getChildControl("toolbar");
        if (!Acl.hasRight("training_centers.C")) {
            toolbar.getChildControl("add-button").exclude();
        }
        if (!Acl.hasRight("training_centers.D")) {
            toolbar.getChildControl("delete-selected-button").exclude();
        }

        var table, tableModel;
        
        tableModel = new frontend.lib.ui.table.model.Remote().set({
            dataUrl : Urls.resolve("TRAINING_CENTERS")
        });
        tableModel.setColumns(
            ["Nazwa", "Ulica", "Miasto", "Liczba sal", "Liczba miejsc"],
            ["name", "street", "city", "room_amount", "seats_amount"]
        );
        tableModel.setDataFormatter("city_zip_code", function(rowData){
            return qx.lang.String.format("%1, %2", [rowData.zip_code, rowData.city]);
        }, this);
        table = new frontend.lib.ui.table.List().set({
            renderer : function(rowData)
            {
                this.addTitle(rowData.name);

                this.addLeft(rowData.street, "Ulica");
                this.addLeft(rowData.city_zip_code, "Adres");

                this.addLeft(rowData.seats_amount, "Liczba miejsc");

                this.addButton("edit");
                this.addButton("delete");
            },
            rowHeight       : 110,
            tableModel      : tableModel,
            addFormClass    : "frontend.app.form.training_center.Add",
            editFormClass   : "frontend.app.form.training_center.Edit",
            showCheckboxes  : Acl.hasRight("training_centers.D")
        });
        this.addTab("Ośrodki szkoleniowe", table);
    },

    members :
    {
        
    }
});