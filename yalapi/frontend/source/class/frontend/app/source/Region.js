qx.Class.define("frontend.app.source.Region",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id: 1, label: "Wiejski (gminy wiejskie, wiejsko-miejskie oraz miasta do 25 tys. mieszkańców)" },
            { id: 2, label: "Miejski" }
        ];

        this.setData(data);
    }
});