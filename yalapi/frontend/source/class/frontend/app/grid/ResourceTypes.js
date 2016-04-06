qx.Class.define("frontend.app.grid.ResourceTypes",
{
    extend : frontend.lib.grid.Abstract,

    construct : function()
    {
        this.base(arguments);

        this._table.setShowEditRemove(true);
    },

    members :
    {
        _addActions : true,
        _tableModelUrl              : "RESOURCE_TYPES",
        _tableKeys                  : ["name"],
        _tableColumnNames           : ["Nazwa zasobu"],

        addFormClass     : frontend.app.form.ResourceType,
        editFormClass    : frontend.app.form.ResourceType
    }
});