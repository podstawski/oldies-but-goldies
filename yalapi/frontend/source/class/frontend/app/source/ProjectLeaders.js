qx.Class.define("frontend.app.source.ProjectLeaders",
{
    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);
        this.setUrl(Urls.resolve("PROJECT_LEADERS"));
        this.setDataKey("username");
    },

    members :
    {

    }
});