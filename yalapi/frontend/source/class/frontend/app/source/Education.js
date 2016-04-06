qx.Class.define("frontend.app.source.Education",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id: "null", label: "Brak" },
            { id: 1, label: "Podstawowe" },
            { id: 2, label: "Gimnazjalne" },
            { id: 3, label: "Ponadgimnazjalne" },
            { id: 4, label: "Pomaturalne" },
            { id: 5, label: "Wyższe" }
        ];

        this.setData(data);
    }
});