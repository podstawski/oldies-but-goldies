qx.Class.define("frontend.app.source.Poland",
{
    type : "singleton",
    
    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);
        this.setUrl(Urls.resolve("POLAND"));
        this.setDataKey("name");
    },

    members :
    {
        _transformData : function(data)
        {
            if (data) {
                data.map(function(dataEntry){
                    dataEntry.label = dataEntry.name;
                    delete dataEntry.name;
                });
            }
            return data;
        },

        getByParent : function(parentID)
        {
            return this.getData().filter(function(dataEntry){
                return dataEntry.parent_id == parentID;
            });
        }
    }
});