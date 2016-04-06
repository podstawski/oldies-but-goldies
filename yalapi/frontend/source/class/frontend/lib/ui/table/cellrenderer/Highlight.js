qx.Class.define("frontend.lib.ui.table.cellrenderer.Highlight",
{
    extend : qx.ui.table.cellrenderer.Default,

    properties :
    {
        highlightText :
        {
            check : "String",
            nullable : true,
            apply : "_applyHighlight"
        }
    },

    members :
    {
        __regex : null,

        __escape : null,

        _applyHighlight : function(value, old)
        {
            if (value) {
                this.__regex  = new RegExp(value.replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1"), "gi");
                this.__escape = qx.bom.String.escape(value);
            } else {
                this.__regex  = null;
                this.__escape = null;
            }
        },

        _getContentHtml : function(cellInfo)
        {
            var value = cellInfo.value;
            if (this.__regex) {
                value = value.replace(this.__regex, "<strong>" + this.__escape + "</strong>");
            }
            return value;
        }
    }
});