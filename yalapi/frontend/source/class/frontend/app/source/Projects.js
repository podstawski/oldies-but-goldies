qx.Class.define("frontend.app.source.Projects",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        this.setUrl(Urls.resolve('PROJECTS'));
        this.setDataKey("name_and_code");
    },

    members :
    {
        _transformData : function(data)
        {
            for (var i = 0, l = data.length; i < l; i++) {
                data[i].name_and_code = data[i].name + " (" + data[i].code + ")";
            }
            return data;
        }
    }
});