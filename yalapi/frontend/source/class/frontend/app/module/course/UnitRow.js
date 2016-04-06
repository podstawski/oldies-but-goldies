/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/list-remove.png)

********************************* */

qx.Class.define("frontend.app.module.course.UnitRow",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "multi-items-row"
        },

        rowId :
        {
            check : "PositiveInteger",
            init : null,
            nullable : true,
            event : "changeRowId"
        }
    },

    events :
    {
        "changeRowId" : "qx.event.type.Data"
    },

    construct : function()
    {
        this.base(arguments);
        
        this.setLayout(new qx.ui.layout.HBox(10));
        
        this.add(this.getChildControl("lp"));
        this.add(this.getChildControl("name"));
        this.add(this.getChildControl("hour-amount"));
        this.add(this.getChildControl("coach-id"));
        this.add(this.getChildControl("remove-button"));
        this.add(this.getChildControl("add-coach-button"));
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "lp":
                    control = new qx.ui.basic.Label().set({
                        width : 25
                    });
                    break;
                
                case "name":
                    control = new frontend.lib.ui.form.TextField().set({
                        required    : true,
                        width       : 200
                    });
                    break;

                case "hour-amount":
                    control = new frontend.lib.ui.form.Spinner(0, 0, 1000).set({
                        required    : true,
                        width       : 80
                    });
                    break;

                case "coach-id":
                    control = new frontend.lib.ui.form.ComboTable().set({
                        required    : true,
                        placeholder : "proszę wybrać trenera",
                        width       : 200,
                        dataColumn  : function(rowData){
                            return qx.lang.String.format("%1 %2 (%3)", [ rowData.first_name, rowData.last_name, rowData.username ]);
                        },
                        dataUrl     : Urls.resolve("COACHES")
                    });
                    break;

                case "remove-button":
                    control = new qx.ui.basic.Image("list-remove");
                    control.setCursor("pointer");
                    control.setToolTipText("usuń pozycję");
                    control.exclude();
                    break;

                case "add-coach-button":
                    control = new qx.ui.form.Button("dodaj trenera", "user-profile");
                    control.setCursor("pointer");
                    control.exclude();
                    break;
            }
            
            return control || this.base(arguments, id);
        },

        getValues : function()
        {
            var dataEntry           = {}
            dataEntry.name          = this.getChildControl("name").getValue();
            dataEntry.hour_amount   = this.getChildControl("hour-amount").getValue();
            dataEntry.user_id       = this.getChildControl("coach-id").getModel();

            var rowID = this.getRowId();
            if (rowID) {
                dataEntry.id = rowID;
            }
            return dataEntry;
        },

        populate : function(data)
        {
            if (data.name) {
                this.getChildControl("name").setValue(data.name);
            }
            if (data.hour_amount) {
                var houramount = this.getChildControl("hour-amount");
                houramount.setValue(data.hour_amount);
                if (data.planned) {
                    houramount.setMinimum(data.planned);
                }
            }
            if (data.user_id) {
                this.getChildControl("coach-id").setModel(data.user_id);
            }
            if (data.id) {
                this.setRowId(data.id);
            }
        }
    }
});