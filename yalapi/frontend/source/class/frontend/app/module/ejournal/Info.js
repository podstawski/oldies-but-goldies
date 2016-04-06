qx.Class.define("frontend.app.module.ejournal.Info",
{
    extend : qx.ui.container.Composite,

    events :
    {
        "changeGroupName" : "qx.event.type.Data",
        "changeCourseName" : "qx.event.type.Data",
        "changeUnitName" : "qx.event.type.Data",
        "changeTrainerName" : "qx.event.type.Data"
    },

    properties :
    {
        groupName :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeGroupName"
        },

        courseName :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeCourseName"
        },

        unitName :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeUnitName"
        },

        trainerName :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeTrainerName"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox);

        this.add(this.getChildControl("label#group"));
        this.add(this.getChildControl("label#course"));
        this.add(this.getChildControl("label#unit"));
        this.add(this.getChildControl("label#trainer"));
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "label":
                    control = new qx.ui.basic.Label();
                    control.setRich(true);
                    var template = "<span style=\"width:70px;display:inline-block;\">%1:</span><b>%2</b>";
                    this.bind(hash + "Name", control, "value", {
                        converter : function(value) {
                            return qx.lang.String.format(template, [Tools["tr"]("ejournal.info.label." + hash), value]);
                        }
                    });
                    this.bind(hash + "Name", control, "visibility", {
                        converter : function(value) {
                            return !!value ? "visible" : "excluded";
                        }
                    });
                    break;
            }

            return control || this.base(arguments, id);
        }
    }
});