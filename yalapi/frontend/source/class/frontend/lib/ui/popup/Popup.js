qx.Class.define("frontend.lib.ui.popup.Popup",
{
    extend:qx.ui.popup.Popup,

    include:[
        qx.ui.core.MMovable
    ],

    construct:function (layout)
    {
        this.base(arguments, layout);
        this.setPadding(10);
        this.setMovable(false);
        this._activateMoveHandle(this);
    }
});