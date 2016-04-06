qx.Mixin.define("frontend.lib.ui.form.MSourceProperty",
{
    events :
    {
        "changeSource" : "qx.event.type.Data",
        "changeSourceData" : "qx.event.type.Event"
    },

    properties :
    {
        source :
        {
            check : "frontend.app.source.Source || Array || String",
            init : frontend.app.source.Source,
            event : "changeSource",
            apply : "__applySource"
        }
    },

    construct : function()
    {
        this.__sourceInstance = new frontend.app.source.Source();
    },

    members :
    {
        __sourceInstance : null,

        __applySource : function(value)
        {
            var clazz = qx.lang.Type.getClass(value);
            var old = this.__sourceInstance;
            
            if (clazz === "String") {
                var app = frontend.app;
                try {
                    this.__sourceInstance = app.source[value].getInstance();
                } catch (ex) {
                    try {
                        this.__sourceInstance = new app.source[value]();
                    } catch (ex) {}
                }
            } else if (clazz === "Array") {
                this.__sourceInstance = new frontend.app.source.Source().set({
                    data : value
                });
            } else if (value instanceof frontend.app.source.Source) {
                this.__sourceInstance = value;
            } else {
                throw new Error("Source must be a string, " +
                                "an array or instanceof frontend.app.source.Source");
            }

            if (this._applySource) {
                this._applySource(this.__sourceInstance, old);
            }

            this.__sourceInstance.addListener("changeData", function(e){
                this.fireEvent("changeSourceData");
            }, this);
        },

        getSourceInstance : function()
        {
            return this.__sourceInstance;
        },

        getSourceData : function()
        {
            return this.getSourceInstance().getData();
        },

        getSourceDataKey : function()
        {
            return this.getSourceInstance().getDataKey();
        }
    }
});