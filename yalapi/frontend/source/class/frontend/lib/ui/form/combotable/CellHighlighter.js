qx.Class.define("frontend.lib.ui.form.combotable.CellHighlighter",
{
    extend : combotable.CellHighlighter,

    members :
    {
        _getContentHtml : function(cellInfo)
        {
            var str = this._formatValue(cellInfo);

            if (this.__searchEsc)
            {
                this.__searchEsc.split(/\W+/).forEach(function(rx){
                    str = str.replace(new RegExp(rx, "i"), "<strong>" + rx + "</strong>");
                }, this);
            }

            return str;
        }
    }
});