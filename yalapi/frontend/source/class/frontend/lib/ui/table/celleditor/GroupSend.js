qx.Class.define("frontend.lib.ui.table.celleditor.GroupSend",
{
    extend : qx.core.Object,

    implement : [
        qx.ui.table.ICellEditorFactory
    ],

    members :
    {
        createCellEditor : function(cellInfo)
        {
            var cellEditor = new frontend.lib.ui.form.DateField()
            return cellEditor;
        },

        getCellEditorValue : function(cellEditor)
        {
            return cellEditor.getValue();
        }
    }
});
