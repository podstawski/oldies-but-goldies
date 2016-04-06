qx.Class.define("frontend.lib.ui.form.combotable.SearchableModel",
{
    extend : combotable.SearchableModel,

    properties :
    {
        searchAsRegEx :
        {
            refine : true,
            init : true
        }
    },
    
    members :
    {
        _applySearchString : function(newString, oldString)
        {
            if (this.getSearchAsRegEx()) {
                newString = newString.replace(/\W+/g, ".*");
            }
            this.base(arguments, newString, oldString);
        }
    }
})