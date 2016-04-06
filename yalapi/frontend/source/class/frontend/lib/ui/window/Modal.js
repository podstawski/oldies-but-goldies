qx.Class.define("frontend.lib.ui.window.Modal",
{
    extend : frontend.lib.ui.window.Window,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "window-modal"
        }
    },

    construct : function(caption, icon)
    {
        this.base(arguments, caption, icon);

        this.setModal(true);
        this.setResizable(false);
        this.setMaxHeight(600);
        this.setLayout(new qx.ui.layout.VBox(10));
        
        this.center();
    }
});