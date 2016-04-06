qx.Class.define("frontend.app.source.ProjectTypes",
{
    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        this.setData([
            { id : 1, level : "Bieżący" },
            { id : 2, level : "Planowany" },
            { id : 3, level : "Archiwalny" }
        ]);
        this.setDataKey("status");
    }
});