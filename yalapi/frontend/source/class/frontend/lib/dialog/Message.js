qx.Class.define("frontend.lib.dialog.Message",
{
    extend : frontend.lib.dialog.Dialog,

    construct : function(message)
    {
        this.base(arguments, message);
        
        qx.event.Timer.once(this._startFadeEffect, this, 250);
    },

    members :
    {
        _startFadeEffect : function()
        {
            var fadeEffect = new qx.fx.effect.core.Fade(this.getContainerElement().getDomElement());
            fadeEffect.setDuration(0.5);
            fadeEffect.addListener("finish", function(e){
                this.close();
            }, this);
            fadeEffect.start();
        }
    }
});