qx.Class.define("frontend.lib.ui.basic.Link",
{
    extend : qx.ui.basic.Label,

    members :
    {
        // overriden
        _applyValue : function(value, old)
        {
            if (this.getRich() !== true) {
                this.setRich(true);
            }
            this.getContentElement().setValue("<a href=\"javascript:void(null);\">" + value + "</a>");

            // Mark text size cache as invalid
            this.__invalidContentSize = true;

            // Update layout
            qx.ui.core.queue.Layout.add(this);
        }
    }
});