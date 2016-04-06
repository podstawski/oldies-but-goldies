qx.Class.define("frontend.app.source.Languages",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [];

        qx.locale.Manager.getInstance().getAvailableLocales().forEach(function(language){
            data.push({
                id : language,
                label : Tools["tr"]("Lang:" + language),
                icon : "frontend/flags/" + language + ".png"
            });
        }, this);

        this.setData(data);
        this.setDataKey("label");
    }
});