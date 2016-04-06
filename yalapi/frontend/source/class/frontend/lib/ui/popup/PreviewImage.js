qx.Class.define("frontend.lib.ui.popup.PreviewImage",
{
    extend : qx.ui.window.Window,

    properties :
    {
        imageUrl :
        {
            check : "String",
            nullable : true,
            init : null,
            apply : "_applyImageUrl"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.set({
            layout : new qx.ui.layout.VBox(),
            modal : true,
            showMinimize : false,
            showMaximize : false,
            contentPadding : 0
        });

        this._image = new qx.ui.basic.Image();

        this.add(this._image, {flex:1});
    },

    members :
    {
        _image : null,

        _applyImageUrl : function(value)
        {
            this._image.setSource(value);
        }
    }
});