/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/edit-paste.png)
#asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)

********************************** */

qx.Class.define("frontend.lib.ui.table.cellrenderer.Buttons",
{
    extend : qx.ui.table.cellrenderer.Abstract,

    construct : function()
    {
        this.base(arguments);

        this.__am = qx.util.AliasManager.getInstance();
        this.__rm = qx.util.ResourceManager.getInstance();
    },

    members :
    {
        __am : null,
        __rm : null,

        _getContentHtml : function(cellInfo)
        {
            var buttons = cellInfo.value;
            var content = [];

            if (buttons) {
                qx.lang.Object.getKeys(buttons).forEach(function(key){
                    content.push(
                        '<div style="display:inline-block;background-image:url(' + this.__rm.toUri(
                            this.__am.resolve(buttons[key])
                        ) + ');height:16px;width:16px;cursor:pointer;margin:0 6px;" action="' + key + '" title="' + Tools["tr"]("table.row.button:" + key) + '"></div>'
                    );
                }, this);
            }

            return content.join("");
        },

        _getCellClass : function(cellInfo)
        {
            return "qooxdoo-table-cell qooxdoo-table-cell-icon";
        }
    }
});