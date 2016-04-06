qx.Class.define("frontend.app.source.Resources",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);
        this.setUrl(Urls.resolve('RESOURCE_TYPES'));
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