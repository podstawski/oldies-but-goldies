qx.Class.define("frontend.app.source.Statuses",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id: 1, label: "Bieżące"},
            { id: 2, label: "Planowane"},
            { id: 3, label: "Archiwalne"}
        ];

        this.setData(data);
    }
});