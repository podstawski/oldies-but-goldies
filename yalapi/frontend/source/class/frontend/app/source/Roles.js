qx.Class.define("frontend.app.source.Roles",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id : 1, label : "admin", name : "admin" },
            { id : 3, label : "lider", name : "project_leader" },
            { id : 5, label : "nauczyciel", name : "trainer" },
            { id : 2, label : "uczeń", name : "user" }
        ];

        this.setData(data);
    },

    members :
    {
        getRoleName : function(roleID)
        {
            return this.getById(roleID).name;
        }
    }
});
