qx.Class.define("frontend.lib.ui.table.headerrenderer.Boolean",
{
    extend : frontend.lib.ui.table.headerrenderer.Default,

    properties :
    {
        canEdit :
        {
            check : "Boolean",
            init : false,
            apply : "_applyCanEdit"
        }
    },

    members :
    {
        checkbox : null,

        createHeaderCell : function(cellInfo)
        {
            var widget = new frontend.lib.ui.table.headerrenderer.CheckboxCell(cellInfo);
            this.updateHeaderCell(cellInfo, widget);
            this.checkbox = widget.getChildControl("checkbox");
            if (!this.getCanEdit()) {
                this.checkbox.exclude();
            }
            return widget;
        },

        _applyCanEdit : function(value)
        {
            if (this.checkbox) {
                if (value) {
                    this.checkbox.show();
                } else {
                    this.checkbox.exclude();
                }
            }
        }
    }
});