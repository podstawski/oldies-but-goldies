qx.Class.define("frontend.app.source.Levels",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        this.setData([
            { id : "null", level : "- nie dotyczy -" },
            { id : 1, level : "podstawowy" },
            { id : 2, level : "średni" },
            { id : 3, level : "zaawansowany" }
        ]);
        this.setDataKey("level");
    }
});