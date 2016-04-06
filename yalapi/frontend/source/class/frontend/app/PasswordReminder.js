qx.Class.define("frontend.app.PasswordReminder",
{
    extend : frontend.lib.form.Abstract,
    construct: function() {
        this.base(arguments);
        var label = new qx.ui.basic.Label(Tools.tr('form.passwordReminder:label'));
        label.setRich(true);
        this.add(label);
    },
    members :
    {
        _caption    : 'form.passwordReminder:window',
        _prefix     : 'form.passwordReminder',
        _url        : Urls.resolve('remind_password'),
        _template   :
        {
            email: {
                type : "TextField",
                properties: {
                    required: true
                },
                validators: [
                    Validate.email()
                ]
            }
        }
    }
});