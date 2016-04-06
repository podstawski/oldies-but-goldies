qx.Class.define("frontend.lib.ui.tooltip.ErrorToolTip",
{
    extend : qx.ui.tooltip.ToolTip,

    construct : function(label, icon)
    {
        this.base(arguments, label, icon);
        this.setAppearance("tooltip-error");
        this.syncAppearance();
    },

    members :
    {
        placeToWidget : function (target, liveupdate)
        {
            if (target instanceof frontend.lib.ui.form.PolandSelect) {
                var parts = ["province", "district", "community"];
                for (var i in parts) {
                    var tmp = target.getChildControl(parts[i]);
                    if (!!(tmp.getSelection()[0].getModel()) == false) {
                        target = tmp;
                        break;
                    }
                }
            }
            this.base(arguments, target, liveupdate);
        }
    }
});