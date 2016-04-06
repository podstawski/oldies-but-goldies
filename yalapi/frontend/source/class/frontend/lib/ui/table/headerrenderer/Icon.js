qx.Class.define("frontend.lib.ui.table.headerrenderer.Icon",
{
    extend : qx.ui.table.headerrenderer.Icon,

    members :
    {
        updateHeaderCell : function(cellInfo, cellWidget)
        {
            this.base(arguments, cellInfo, cellWidget);

            cellWidget._excludeChildControl("label");

            var layout = cellWidget._getLayout();
            layout.setColumnFlex(0, 1);
            layout.setColumnFlex(1, 0);
            layout.setColumnAlign(0, "center", "middle");
        }
    }
});