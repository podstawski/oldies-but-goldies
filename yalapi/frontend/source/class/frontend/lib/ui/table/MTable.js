qx.Mixin.define("frontend.lib.ui.table.MTable",
{
    properties :
    {
        addFormClass :
        {
            check : "String",
            validate : "__validateClass",
            init : null
        },

        editFormClass :
        {
            check : "String",
            validate : "__validateClass",
            init : null
        }
    },

    members :
    {
        __validateClass : function(value)
        {
            if (!qx.Class.getByName(value)) {
                throw new qx.core.ValidationError(null, "Value must be a name of an existing class!");
            }
        }
    }
});