/* *********************************

#asset(qx/icon/${qx.icontheme}/32/status/dialog-warning.png)
#asset(qx/icon/${qx.icontheme}/22/actions/dialog-apply.png)
#asset(qx/icon/${qx.icontheme}/22/actions/dialog-cancel.png)

********************************** */

qx.Class.define("frontend.lib.dialog.Confirm",
{
    extend : frontend.lib.dialog.Dialog,

    events :
    {
        "yes" : "qx.event.type.Event",
        "no" : "qx.event.type.Event"
    },

    construct : function(message)
    {
        this.base(arguments, message, "icon/48/status/dialog-warning.png");

        var buttonsLayout    = new qx.ui.layout.HBox(10).set({alignX: "center"});
        var buttonsContainer = new qx.ui.container.Composite(buttonsLayout);

        var yesButton = new qx.ui.form.Button(Tools.tr("dialog.yes"), "icon/22/actions/dialog-apply.png").set({width: 100});
        var noButton  = new qx.ui.form.Button(Tools.tr("dialog.no"), "icon/22/actions/dialog-cancel.png").set({width: 100});

        yesButton.addListener("execute", function(e){
            this.fireEvent("yes");
            this.close();
        }, this);

        noButton.addListener("execute", function(e){
            this.fireEvent("no");
            this.close();
        }, this);

        buttonsContainer.add(yesButton);
        buttonsContainer.add(noButton);

        this.add(buttonsContainer);
    }
});