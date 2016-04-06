qx.Class.define("frontend.lib.ui.table.pane.Scroller",
{
    extend : qx.ui.table.pane.Scroller,

    members :
    {
        _onClickHeader : function(e)
        {
            this.__ignoreClick = (e.getTarget().getAppearance() == "checkbox");
            this.base(arguments, e);
        }
    }
});