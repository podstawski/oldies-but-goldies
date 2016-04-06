qx.Class.define("frontend.lib.ui.table.celleditor.Grade",
{
    extend : qx.core.Object,

    implement : [
        qx.ui.table.ICellEditorFactory
    ],

    members :
    {
        createCellEditor : function(cellInfo)
        {
            var table = cellInfo.table;
            var cellEditor = new frontend.lib.ui.form.SelectBox().set({
                appearance : "table-editor-selectbox",
                source     : "Grades"
            });
            cellEditor.setModelSelection([cellInfo.value || null]);
            cellEditor.addListener("changeSelection", function(e){
                table.stopEditing();
            }, this);
            return cellEditor;
        },

        getCellEditorValue : function(cellEditor)
        {
            return cellEditor.getSelection()[0].getModel();
        }
    }
});