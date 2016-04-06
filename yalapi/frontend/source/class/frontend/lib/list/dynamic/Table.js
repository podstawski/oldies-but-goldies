qx.Class.define("frontend.lib.list.dynamic.Table",
{
    extend : frontend.lib.ui.table.Table,

    properties :
    {
        columnVisibilityButtonVisible :
        {
            refine : true,
            init : false
        }
    },

    construct : function(tableModel, custom)
    {
        this.base(arguments, tableModel, qx.lang.Object.merge(custom || {}, {
            tableColumnModel : function(obj) {
                return new qx.ui.table.columnmodel.Basic(obj);
            }
        }));
    }
});