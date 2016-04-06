qx.Class.define("frontend.lib.ui.Filter",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-filter"
        },

        visibility :
        {
            refine : true,
            init : "excluded"
        }
    },

    events :
    {
        "changeFilterValue" : "qx.event.type.Data"
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.HBox(3));

        var selectbox = this._createChildControl("selectbox");
        selectbox.addListener("changeSource", this._onChangeSource, this);
        selectbox.addListener("changeSelection", function(e){
            var id = e.getData()[0].getModel();
            this.fireDataEvent("changeFilterValue", id);
        }, this);

        this._add(this._createChildControl("label"));
        this._add(selectbox);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "label":
                    control = new qx.ui.basic.Label().set({
                        alignY : "middle",
                        marginRight : 10
                    });
                    break;

                case "selectbox":
                    control = new frontend.lib.ui.form.SelectBox().set({
                        defaultOption : "- pokaż wszystko -",
                        width : 150
                    });
                    break;
            }

            return control || this.base(arguments, id);
        },

        _onChangeSource : function(e)
        {
            var source = e.getData();
            if (source) {
                this.setVisibility("visible");
            } else {
                this.setVisibility("excluded");
            }
        },

        setSource : function(source)
        {
            this.getChildControl("selectbox").setSource(source);
            return this;
        },

        setLabel : function(label)
        {
            this.getChildControl("label").setValue(label);
            return this;
        }
    }
});