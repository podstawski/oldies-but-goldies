qx.Class.define("frontend.lib.ui.Logo",
{
    extend : qx.ui.basic.Image,

    properties :
    {
        source :
        {
            refine : true,
            init : "frontend/logo.jpg"
        },

        width :
        {
            refine : true,
            init : 286
        },

        height :
        {
            refine : true,
            init : 100
        },

        scale :
        {
            refine : true,
            init : true
        }
    }
});