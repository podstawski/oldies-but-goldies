qx.Class.define("frontend.app.source.PersonalStatus",
{
    type : "singleton",

    extend : frontend.app.source.Source,

    construct : function()
    {
        this.base(arguments);

        var data = [
            { id: 1, label: "uczący się"},
            { id: 2, label: "pracujący"}
        ];

        this.setData(data);
    }
});