qx.Class.define("frontend.lib.ui.form.RadioGroup",
{
    extend : qx.ui.form.RadioButtonGroup,

    include : [
        frontend.lib.ui.form.MSourceProperty
    ],

    properties :
    {
        orientation :
        {
            check : [ "vertical", "horizontal" ],
            nullable : false,
            init : "horizontal",
            apply : "_applyOrientation"
        }
    },

    construct : function(layout)
    {
        this.base(arguments, layout);

        if (!layout) {
            this.initOrientation();
        }
    },

    members :
    {
        _applyOrientation : function(value, old)
        {
            if (value == "vertical") {
                this.setLayout(new qx.ui.layout.VBox(5));
            } else {
                this.setLayout(new qx.ui.layout.HBox(10));
            }
        },

        _applySource : function(source, old)
        {
            if (old) {
                old.removeListener("changeData", this._updateData, this);
            }

            if (source)
            {
                source.addListener("changeData", this._updateData, this);
                this._updateData();
            }
        },

        _updateData : function()
        {
            this.removeAll();

            var key  = this.getSourceDataKey();
            var data = this.getSourceData();

            if (data)
            {
                data.forEach(function(dataEntry, index){
                    this.add(
                        new qx.ui.form.RadioButton(
                            dataEntry[key]
                        ).set({
                            model : dataEntry["id"],
                            rich : true
                        })
                    )
                }, this);
            }
        }
    }
});