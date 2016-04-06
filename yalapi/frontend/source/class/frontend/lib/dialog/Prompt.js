qx.Class.define("frontend.lib.dialog.Prompt",
{
    extend : frontend.lib.dialog.Dialog,

    events :
    {
        "ok"     : "qx.event.type.Data",
        "cancel" : "qx.event.type.Event"
    },

    construct : function(message)
    {
        this.base(arguments, message);

        var textfield = new frontend.lib.ui.form.TextField();
        textfield.setRequired(true);

        var validator = new frontend.lib.ui.form.validation.Manager();
        validator.add(textfield, Validate.string(), this);
        validator.addListener("complete", function(e){
            if (validator.isValid()) {
                this.fireDataEvent("ok", textfield.getValue());
                this.close();
            }
        }, this);

        var buttonsContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "center"));

        var okButton = new qx.ui.form.Button(Tools.tr("dialog.ok"), "22-button-submit");
        okButton.setWidth(100);

        var cancelButton = new qx.ui.form.Button(Tools.tr("dialog.cancel"), "22-button-cancel");
        cancelButton.setWidth(100);

        okButton.addListener("execute", validator.validate, this);

        cancelButton.addListener("execute", function(e){
            this.fireEvent("cancel");
            this.close();
        }, this);

        buttonsContainer.add(okButton);
        buttonsContainer.add(cancelButton);

        this.add(textfield);
        this.add(buttonsContainer);
    }
});