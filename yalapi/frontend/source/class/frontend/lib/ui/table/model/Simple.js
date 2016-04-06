qx.Class.define("frontend.lib.ui.table.model.Simple",
{
    extend : qx.ui.table.model.Simple,

    include : [
        frontend.lib.ui.MSearchBox,
        frontend.lib.ui.table.model.MFormatRowData
    ],

    members :
    {
        reloadData : function()
        {
            
        },

        _onChangeSearchValue : function(e)
        {
            this.reloadData();
        }
    }
});