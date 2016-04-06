qx.Class.define("frontend.lib.ui.table.cellrenderer.Grade",
{
    extend : frontend.lib.ui.table.cellrenderer.Conditional,

    include : [
        frontend.lib.util.MGetSource
    ],

    construct : function()
    {
        this.base(arguments, "center");
        
//        this.addNumericCondition(">=", 1.00, null, "red");
//        this.addNumericCondition(">=", 2.75, null, "orange");
//        this.addNumericCondition(">=", 3.75, null, null);
//        this.addNumericCondition(">=", 4.75, null, "green");

        this.addNumericCondition(">", 0, "center", null, null, "bold");
    },

    members :
    {
        _getContentHtml : function(cellInfo)
        {
            var value = cellInfo.value;
            if (!value) {
                return "";
            }
            return this.getSourceInstance("Grades").getById(value).label;
        }
    }
});