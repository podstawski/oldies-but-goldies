qx.Class.define("frontend.lib.ui.form.TimeField",
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
            init : "timefield"
        }
    },

    construct : function(value)
    {
        this.base(arguments);

        var layout = new qx.ui.layout.Grid();
        layout.setColumnFlex(0, 1);
        layout.setColumnFlex(2, 1);
        this._setLayout(layout);

        this._createChildControl("hourfield");
        this._createChildControl("separator");
        this._createChildControl("minutefield");

        this.setAllowStretchX(false);

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
                minimumIntegerDigits : 2,
                maximumIntegerDigits : 2,
                maximumFractionDigits : 0
            });
            var control;

            switch(id)
            {
                case "hourfield":
                    control = new frontend.lib.ui.form.NumberField().set({
                        decorator : null,
                        maximum : 23,
                        numberFormat : numberFormat
                    });
                    control.getChildControl("textfield").set({
                        width : 25,
                        textAlign : "right"
                    });
                    control.addState("inner");
                    this._add(control, {column: 0, row: 0});
                    break;

                case "separator":
                    control = new qx.ui.basic.Label(":").set({
                        alignY : "middle"
                    });
                    control.addState("inner");
                    this._add(control, {column: 1, row: 0});
                    break;
                
                case "minutefield":
                    control = new frontend.lib.ui.form.NumberField().set({
                        decorator : null,
                        maximum : 59,
                        numberFormat : numberFormat
                    });
                    control.getChildControl("textfield").set({
                        width : 25,
                        textAlign : "left"
                    });
                    this._add(control, {column: 2, row: 0});
                    break;
            }

            return control || this.base(arguments, id);
        },

        setValue : function(value)
        {
            if (new RegExp("^[0-9]{1,2}:[0-9]{1,2}$").test(value)) {
                this.getChildControl("hourfield").setValue(value.split(":")[0]);
                this.getChildControl("minutefield").setValue(value.split(":")[1]);
            } else {
                throw new Error("złe value");
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