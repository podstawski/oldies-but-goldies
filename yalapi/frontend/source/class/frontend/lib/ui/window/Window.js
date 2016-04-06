qx.Class.define("frontend.lib.ui.window.Window",
{
    extend : qx.ui.window.Window,

    members :
    {
        // overriden
        center : function()
        {
            if (this.isSeeable() == false) {
                this.addListenerOnce("appear", this.center, this);
                return;
            }

            var parent = qx.core.Init.getApplication().getRoot();
            if (parent) {
                var bounds = parent.getBounds();
                if (bounds) {
                    var hint = this.getBounds();

                    var left = Math.max(Math.round((bounds.width  - hint.width)  / 2), 0);
                    var top  = Math.max(Math.round((bounds.height - hint.height) / 2), 0);

                    this.moveTo(left, top);
                }
            }
        }
    }
});