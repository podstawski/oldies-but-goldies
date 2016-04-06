qx.Class.define("frontend.lib.ui.form.SelectBox",
{
    extend : qx.ui.form.SelectBox,

    include : [
        frontend.lib.ui.form.MSourceProperty
    ],

    events :
    {
        "changeDefaultOption" : "qx.event.type.Data"
    },

    properties :
    {
        defaultOption :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeDefaultOption",
            apply : "_applyDefaultOption"
        },

        selectOnlyOption :
        {
            check : "Boolean",
            init : false
        }
    },

    members :
    {
        _applyDefaultOption : function(value, old)
        {
            if (this.hasChildren()) {
                this.removeAll();
            }

            var key  = this.getSourceDataKey();
            var data = this.getSourceData();

            if (data)
            {
                data.forEach(function(dataEntry, index){
                    if (value == dataEntry.id) {
                        this.add(
                            new qx.ui.form.ListItem(
                                dataEntry[key],
                                dataEntry["icon"] || null,
                                dataEntry["id"]
                            )
                        );
                    }
                }, this);

                data.forEach(function(dataEntry, index){
                    if (value != dataEntry.id){
                        this.add(
                            new qx.ui.form.ListItem(
                                dataEntry[key],
                                dataEntry["icon"] || null,
                                dataEntry["id"]
                            )
                        );
                    }
                }, this);
            }

        },

        _applySource : function(source, old)
        {
            if (old) {
                old.removeListener("changeData", this._updateData, this);
            }
            
            if (source) {
                source.addListener("changeData", this._updateData, this);
                this._updateData();
            }
        },

        _updateData : function()
        {
            if (this.hasChildren()) {
                try {
                    this.removeAll();
                } catch (ex) {
                    
                }
            }

            var key  = this.getSourceDataKey();
            var data = this.getSourceData();

            var defaultOption = this.getDefaultOption();
            if (defaultOption) {
                this.add(
                    new qx.ui.form.ListItem(
                        defaultOption,
                        null,
                        ""
                    )
                );
            }

            if (data)
            {
                data.forEach(function(dataEntry, index){
                    this.add(
                        new qx.ui.form.ListItem(
                            dataEntry[key],
                            dataEntry["icon"] || null,
                            dataEntry["id"]
                        )
                    )
                }, this);

                if (this.getSelectOnlyOption() && data.length == 1) {
                    this.setModelSelection([
                        data[0].id
                    ]);
                }
            }
        }
    }
});