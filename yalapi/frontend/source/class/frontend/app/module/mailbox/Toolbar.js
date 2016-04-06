qx.Class.define("frontend.app.module.mailbox.Toolbar",
{
    extend : qx.ui.toolbar.ToolBar,

    construct : function()
    {
        this.base(arguments);

        this.add(this.getChildControl("compose-button"));
        this.add(this.getChildControl("reply-button"));
        this.add(this.getChildControl("forward-button"));
        this.add(this.getChildControl("delete-button"));
        this.addSpacer();
        this.add(this.getChildControl("searchbox"));
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;
            
            switch (id)
            {
                case "compose-button":
                    control = this._getBaseButton("Nowa wiadomość", "22-mail-compose");
                    break;

                case "reply-button":
                    control = this._getBaseButton("Odpowiedz", "22-mail-reply");
                    break;

                case "forward-button":
                    control = this._getBaseButton("Prześlij dalej", "22-mail-forward");
                    break;

                case "delete-button":
                    control = this._getBaseButton("Usuń", "22-mail-delete");
                    break;

                case "searchbox":
                    control = new frontend.lib.ui.SearchBox();
                    break;
            }

            return control || this.base(arguments, id);
        },

        _getBaseButton : function(value, icon)
        {
            return new frontend.lib.ui.form.Button(value, icon).set({
                margin   : 0,
                minWidth : 100,
                height   : 40,
                enabled  : false
            });
        }
    }
});