qx.Class.define("frontend.lib.ui.table.List",
{
    extend : frontend.lib.ui.table.Grid,

    properties :
    {
        newTablePane :
        {
            refine : true,
            init : function(obj) 
            {
                return new frontend.lib.ui.table.pane.List(obj);
            }
        },

        rowHeight :
        {
            refine : true,
            init : 100
        },

        renderer :
        {
            check : "Function",
            init : qx.lang.Function.empty
        }
    },

    members :
    {
        _applyTableModel : function(tableModel, old)
        {
            this.base(arguments, tableModel, old);
            
            var buttonsColumn = tableModel.getColumnIndexById("extra_buttons");
            if (buttonsColumn != null) {
                this.getTableColumnModel().setColumnVisible(1, false);
            }
        }
    }
});