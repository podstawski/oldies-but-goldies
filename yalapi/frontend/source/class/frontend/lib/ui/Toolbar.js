qx.Class.define("frontend.lib.ui.Toolbar",
{
    extend : qx.ui.toolbar.ToolBar,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-toolbar"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.add(this._createChildControl("add-button"));
        this.add(this._createChildControl("delete-selected-button"));
        this.add(this._createChildControl("refresh-button"));
        this.addSpacer();
        this.add(this._createChildControl("filter"));
        this.add(this._createChildControl("searchbox"));
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "add-button":
                    control = this._getBaseButton("Dodaj", "button-add");
                    break;

                case "delete-selected-button":
                    control = this._getBaseButton("Usuń", "button-delete");
                    break;

                case "refresh-button":
                    control = this._getBaseButton("Odśwież", "button-refresh");
                    break;

                case "filter":
                    control = new frontend.lib.ui.Filter();
                    break;

                case "searchbox":
                    control = new frontend.lib.ui.SearchBox();
                    break;
            }

            return control || this.base(arguments, id);
        },

        _getBaseButton : function(value, icon)
        {
            return new frontend.lib.ui.form.Button(value, icon).set({
                margin: 0,
                minWidth : 100,
                height: 30
            });
        }
    }
});