qx.Class.define("frontend.lib.ui.table.headerrenderer.HeaderCell",
{
    extend : qx.ui.table.headerrenderer.HeaderCell,

    construct : function()
    {
        this.base(arguments);

        var layout = this._getLayout();
        layout.setColumnAlign(1, "center", "middle");
        layout.setColumnFlex(2, 0);
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "label":
                    control = this.base(arguments, id).set({
                        rich : true,
                        textAlign : "center"
                    });
                    break;
            }
            
            return control || this.base(arguments, id);
        }
    }
});