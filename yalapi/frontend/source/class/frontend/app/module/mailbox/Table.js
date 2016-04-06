/* *********************************

#asset(frontend/icons/16/attachment.png)

********************************** */

qx.Class.define("frontend.app.module.mailbox.Table",
{
    extend : frontend.lib.ui.table.Grid,

    construct : function(folder)
    {
        this.base(arguments);

        var columnNames = this.__columns[folder][0];
        var columnIds   = this.__columns[folder][1];
        var columnCount = columnNames.length;
        
        var tableModel = new frontend.lib.ui.table.model.Remote();
        tableModel.setDataUrl(Urls.resolve("MESSAGES"));
        tableModel.setParam("folder", folder);
        tableModel.setColumns(columnNames, columnIds);
        tableModel.setDataFormatter("attachment_icon", function(rowData){
            return !!rowData.attachments ? "frontend/icons/16/attachment.png" : null;
        });

        this.setTableModel(tableModel);
        this.setShowEditRemove(false);
        this.setCheckRowOnDblClick(false);
        this.setShowCheckboxes(true);
        
        var cellRenderer = new frontend.lib.ui.table.cellrenderer.Conditional();
        cellRenderer.addNumericCondition("==", null, null, null, null, "bold", "read_date");

        var tableColumnModel = this.getTableColumnModel();

        tableColumnModel.setDataCellRenderer(tableModel.getColumnIndexById("subject"), cellRenderer);

        var attachmentIconColumnIndex = tableModel.getColumnIndexById("attachment_icon");
        tableColumnModel.setHeaderCellRenderer(attachmentIconColumnIndex, new frontend.lib.ui.table.headerrenderer.Icon("frontend/icons/16/attachment.png", "Załącznik"));
        tableColumnModel.setDataCellRenderer(attachmentIconColumnIndex, new qx.ui.table.cellrenderer.Image());
        tableColumnModel.getBehavior().setWidth(attachmentIconColumnIndex, 50);
        tableModel.setColumnSortable(attachmentIconColumnIndex, false);

        tableModel.sortByColumn(
            tableModel.getColumnIndexById("send_date"),
            false
        );
    },

    members :
    {
        __columns :
        {
            1 :
            [
                ["Załącznik", "Nadawca", "Temat", "Data nadania"],
                ["attachment_icon", "sender", "subject", "send_date"]
            ],
            
            2 :
            [
                ["Załącznik", "Odbiorca" ,"Temat", "Data nadania"],
                ["attachment_icon", "recipient_list", "subject", "send_date"]
            ],

            3 :
            [
                ["Załącznik", "Nadawca", "Odbiorca" ,"Temat", "Data nadania"],
                ["attachment_icon", "sender", "recipient_list", "subject", "send_date"]
            ]
        },

        _onChangeSelection : function(e)
        {
            var rowData = this.getSelectedRowData();
            if (rowData && rowData.read_date == null) {
                this.getTableModel().getRowData(rowData.rowIndex).read_date = new Date;
            }

            this.base(arguments, e);
        }
    }
});