qx.Class.define("frontend.lib.ui.table.headerrenderer.Default",
{
    extend : qx.core.Object,
    
    implement : [
        qx.ui.table.IHeaderRenderer
    ],

    events :
    {
        "changeToolTip" : "qx.event.type.Data"
    },

    properties :
    {
        toolTip :
        {
            check : "String",
            init : null,
            nullable : true,
            event : "changeToolTip"
        }
    },

    members :
    {
        createHeaderCell : function(cellInfo)
        {
            var widget = new frontend.lib.ui.table.headerrenderer.HeaderCell;
            this.updateHeaderCell(cellInfo, widget);
            return widget;
        },

        updateHeaderCell : function(cellInfo, cellWidget)
        {
            var DefaultHeaderCellRenderer = qx.ui.table.headerrenderer.Default;

            if (cellInfo.name && cellInfo.name.translate) {
                cellWidget.setLabel(cellInfo.name.translate());
            } else {
                cellWidget.setLabel(cellInfo.name);
            }

            this.bind("toolTip", cellWidget, "toolTipText");

            cellInfo.sorted ?
                cellWidget.addState(DefaultHeaderCellRenderer.STATE_SORTED) :
                cellWidget.removeState(DefaultHeaderCellRenderer.STATE_SORTED);

            cellInfo.sortedAscending ?
                cellWidget.addState(DefaultHeaderCellRenderer.STATE_SORTED_ASCENDING) :
                cellWidget.removeState(DefaultHeaderCellRenderer.STATE_SORTED_ASCENDING);
        }
    }
});