qx.Class.define("frontend.app.module.mailbox.preview.Window",
{
    extend : frontend.lib.ui.window.Modal,

    construct : function()
    {
        this.base(arguments);

        this.setWidth(800);
        this.setHeight(600);
        this.setAllowShrinkX(false);
        this.setAllowShrinkY(false);
        this.setResizable(true);

        this.add(this.getChildControl("preview"), {flex:1});
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "preview":
                    control = new frontend.app.module.mailbox.Preview().set({
                        paddingTop : 0
                    });
                    break;
            }

            return control || this.base(arguments, id);
        },

        setMessage : function(message)
        {
            this.getChildControl("preview").setMessage(message);
        }
    }
});