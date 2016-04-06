/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/edit-find.png)
#asset(qx/icon/${qx.icontheme}/16/actions/application-exit.png)
#asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)

********************************** */

qx.Class.define("frontend.lib.ui.SearchBox",
{
    extend : qx.ui.container.Composite,

    implement : [
        frontend.lib.ui.ISearchBox
    ],

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-searchbox"
        },

        placeholder :
        {
            check : "String",
            nullable : true,
            apply : "_applyPlaceholder"
        },

        clearSearchBoxOnConnect :
        {
            check : "Boolean",
            init : true
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.HBox(3));

        this.setMinWidth(150);
        this.setWidth(250);

        var textfield = this.getChildControl("textfield");
        var button    = this.getChildControl("clear-button");

        this.add(this.getChildControl("icon"));
        this.add(textfield, {flex: 1});
        this.add(button);

        textfield.bind("value", button, "visibility", {
            converter : function(value) {
                return value ? "visible" : "hidden"
            }
        });
    },

    members :
    {
        _applyPlaceholder : function(value, old)
        {
            this.getChildControl("textfield").setPlaceholder(value);
        },
        
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "textfield":
                    control = new frontend.lib.ui.form.TextField().set({
                        alignY : "middle",
                        liveUpdate : true,
                        placeholder : Tools.tr("searchBox:textfield placeholder")
                    });
                    break;

                case "icon":
                    control = new qx.ui.basic.Image("icon/16/actions/edit-find.png").set({
                        alignY : "middle"
                    });
                    break;

                case "clear-button":
                    control = new qx.ui.basic.Image("icon/16/actions/edit-delete.png").set({
                        alignY : "middle",
                        visibility : "hidden",
                        toolTip : new qx.ui.tooltip.ToolTip().set({
                            label : Tools.tr("searchBox:clear button")
                        })
                    });
                    control.addListener("click", this.clearTextField, this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        getTextField : function()
        {
            return this.getChildControl("textfield", true);
        },

        getSearchField : function()
        {
            return this.getChildControl("textfield", true);
        },

        clearTextField : function()
        {
            var textfield = this.getTextField();
            if (textfield) {
                var old = textfield.getValue();
                textfield.setValue("");
                if (textfield.getLiveUpdate()) {
                    textfield.fireDataEvent("input", "", old);
                } else {
                    textfield.fireDataEvent("changeValue", "", old);
                }
            }
        },

        getSearchValue : function()
        {
            return this.getTextField().getValue();
        }
    }
});