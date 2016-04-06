qx.Class.define("frontend.app.source.ZUS",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id : 1, label : "Umowa jest moim jedynym źródłem dochodu, nigdzie indziej nie pracuję" },
            { id : 2, label : "Jestem zatrudniony/zatrudniona i otrzymuję z tego tytułu co najmniej minimalne wynagrodzenie, i jestem emerytem/rencistą" },
            { id : 3, label : "Jestem studentem, mam poniżej 26 lat" },
            { id : 4, label : "Jestem zatrudniony/zatrudniona i otrzymuję z tego tytułu co najmniej minimalne wynagrodzenie, i nie jestem emerytem/rencistą" },
            { id : 5, label : "Nie jestem nigdzie zatrudniony/zatrudniona i jestem emerytem/rencistą" }
        ];

        this.setData(data);
    }
});