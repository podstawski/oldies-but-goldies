qx.Class.define("frontend.lib.ui.form.TimeFieldSpinner",
{
    extend : qx.ui.core.Widget,

    implement : [
        qx.ui.form.IStringForm,
        qx.ui.form.IForm
    ],

    include : [
        qx.ui.form.MForm
    ],

    events :
    {
        "changeValue" : "qx.event.type.Data"
    },

    properties :
    {
        // overridden
        appearance :
        {
            refine : true,
            init : "timefieldspinner"
        }
    },

    construct : function(value)
    {
        this.base(arguments);

        var layout = new qx.ui.layout.Grid();
        layout.setColumnFlex(0, 1);
        layout.setColumnFlex(2, 1);
        this._setLayout(layout);

        this._add(this.getChildControl("hourfield"),   {column : 0, row : 0});
        this._add(this.getChildControl("separator"),   {column : 1, row : 0});
        this._add(this.getChildControl("minutefield"), {column : 2, row : 0});

        this.setAllowStretchX(false);
        this.setPadding(0);

        if (value) {
            this.setValue(value);
        } else {
            this.resetValue();
        }
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var numberFormat = new qx.util.format.NumberFormat().set({
                minimumIntegerDigits  : 2,
                maximumIntegerDigits  : 2,
                maximumFractionDigits : 0
            });
            var control;

            switch(id)
            {
                case "hourfield":
                    control = new frontend.lib.ui.form.Spinner().set({
                        decorator    : null,
                        minimum      : 0,
                        maximum      : 23,
                        numberFormat : numberFormat
                    });
                    control.getChildControl("textfield").set({
                        width       : 22,
                        textAlign   : "right",
                        marginRight : 2
                    });
                    control.addState("inner");

                    break;

                case "separator":
                    control = new qx.ui.basic.Label(":").set({
                        alignY    : "middle",
                        textAlign : "center",
                        width     : 10
                    });
                    control.addState("inner");
                    break;
                
                case "minutefield":
                    control = new frontend.lib.ui.form.Spinner().set({
                        decorator    : null,
                        minimum      : 0,
                        maximum      : 59,
                        singleStep   : 5,
                        numberFormat : numberFormat
                    });
                    control.getChildControl("textfield").set({
                        width       : 22,
                        textAlign   : "right",
                        marginRight : 2
                    });
                    break;
            }

            return control || this.base(arguments, id);
        },

        setValue : function(value)
        {
            if (new RegExp("^[0-9]{1,2}:[0-9]{1,2}$").test(value)) {
                this.getChildControl("hourfield").setValue(parseInt(value.split(":")[0]));
                this.getChildControl("minutefield").setValue(parseInt(value.split(":")[1]));
            } else {
                this.setInvalidMessage("Could not recognize time format");
                this.setValid(false);
            }
        },

        getValue : function()
        {
            return this.getChildControl("hourfield").getValue() + ":" + this.getChildControl("minutefield").getValue();
        },
        
        resetValue : function()
        {
            this.getChildControl("hourfield").setValue(new Date().getHours());
            this.getChildControl("minutefield").setValue(0);
        }
    }
});