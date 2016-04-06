qx.Class.define("frontend.lib.list.AbstractNotTableOnly",
{
    extend : frontend.lib.list.Abstract,
    type: "abstract",
    members :
    {
        _onChangeTabSelection : function(e){
            return false;
        }
    }

});