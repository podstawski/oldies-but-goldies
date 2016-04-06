qx.Class.define("frontend.lib.ui.table.cellrenderer.Conditional",
{
    extend : qx.ui.table.cellrenderer.Conditional,

    members :
    {
        addNumericCondition : function(condition, value1, align, color, style, weight, target)
        {
            var temp = null;

            if (qx.lang.Array.contains(this.numericAllowed, condition))
            {
                temp = [condition, align, color, style, weight, value1, target];
            }

            if (temp != null) {
                this.conditions.push(temp);
            } else {
                throw new Error("Condition not recognized or value is null!");
            }
        }
    }
});