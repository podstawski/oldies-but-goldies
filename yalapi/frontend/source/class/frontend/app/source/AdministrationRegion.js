qx.Class.define("frontend.app.source.AdministrationRegion",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id: 1, label: "wieś" },
            { id: 2, label: "miasto" }
        ];

        this.setData(data);
    }
});