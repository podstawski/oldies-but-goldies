qx.Class.define("frontend.app.source.SurveyTypes",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        this.setData([
            { id : "survey", name : "Ankieta" },
            { id : "test", name : "Test" }
        ]);
        this.setDataKey("name");
    }
});