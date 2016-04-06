qx.Class.define("frontend.app.source.QuestionTypes",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        this.setData([
            { id : "text", name : "Tekst" },
            { id : "checkboxes", name : "Wielokrotny wybór (checkbox)" },
            { id : "multichoice", name : "Wiele opcji (radio)" },
            { id : "list", name : "Wybór z listy" }
        ]);
        this.setDataKey("name");
    }
});