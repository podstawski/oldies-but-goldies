qx.Class.define("frontend.lib.ui.control.ColorSelector",
{
    extend : qx.ui.control.ColorSelector,

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch(id)
            {
                case "hue-saturation-field":
                    control = new qx.ui.basic.Image("resource/aristo/decoration/colorselector/huesaturation-field.jpg");
                    control.addListener("mousedown", this._onHueSaturationFieldMouseDown, this);
                    break;

                case "hue-saturation-handle":
                    control = new qx.ui.basic.Image("resource/aristo/decoration/colorselector/huesaturation-handle.gif");
                    control.addListener("mousedown", this._onHueSaturationFieldMouseDown, this);
                    control.addListener("mouseup", this._onHueSaturationHandleMouseUp, this);
                    control.addListener("mousemove", this._onHueSaturationHandleMouseMove, this);
                    break;

                case "brightness-field":
                    control = new qx.ui.basic.Image("resource/aristo/decoration/colorselector/brightness-field.png");
                    control.addListener("mousedown", this._onBrightnessFieldMouseDown, this);
                    break;

                case "brightness-handle":
                    control = new qx.ui.basic.Image("resource/aristo/decoration/colorselector/brightness-handle.gif");
                    control.addListener("mousedown", this._onBrightnessHandleMouseDown, this);
                    control.addListener("mouseup", this._onBrightnessHandleMouseUp, this);
                    control.addListener("mousemove", this._onBrightnessHandleMouseMove, this);
                    break;
            }

            return control || this.base(arguments, id, hash);
        }
    }
});