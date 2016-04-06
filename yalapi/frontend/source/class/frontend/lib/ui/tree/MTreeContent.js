qx.Mixin.define("frontend.lib.ui.tree.MTreeContent",
{
    properties :
    {
        content :
        {
            check : "String",
            nullable : true
        },

        menu :
        {
            check : "String",
            nullable : true
        },

        resourceId :
        {
            check : "String",
            nullable : true
        }
    }
});