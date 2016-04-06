qx.Class.define("frontend.lib.ui.table.headerrenderer.Grade",
{
    extend : frontend.lib.ui.table.headerrenderer.Default,

    properties :
    {
        exam :
        {
            check : "Map",
            init : null,
            apply : "_applyExam"
        },

        canEdit :
        {
            check : "Boolean",
            init : false
        }
    },

    events :
    {
        "gradeHeaderClick" : "qx.event.type.Data"
    },

    construct : function()
    {
        this.base(arguments);
    },

    members :
    {
        _applyExam : function(value, old)
        {
            if (value && this.getToolTip() == null) {
                this.setToolTip(value.name);
            }
        },

        createHeaderCell : function(cellInfo)
        {
            var table  = cellInfo.table;
            var widget = new frontend.lib.ui.table.headerrenderer.HeaderCell;
            
            if (this.getCanEdit()) {
                var icon   = widget._showChildControl("icon").set({
                    anonymous : false,
                    source : "icon/16/actions/edit-paste.png",
                    toolTipText : "edytuj",
                    cursor : "pointer"
                });
                var columnIndex = cellInfo.col;
                icon.addListener("click", function(e){
                    table.fireDataEvent("gradeHeaderClick", columnIndex);
                    e.stopPropagation();
                }, this);
            }

            this.updateHeaderCell(cellInfo, widget);
            return widget;
        }
    }
});