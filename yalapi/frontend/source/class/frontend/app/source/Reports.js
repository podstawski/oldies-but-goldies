qx.Class.define("frontend.app.source.Reports",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        this.setUrl(Urls.resolve('REPORT_TEMPLATES'));
        this.setDataKey("name");
    },

    members :
    {
        _transformData : function(data)
        {
            return data;
        }
    }
})