/* *********************************

#asset(qx/icon/${qx.icontheme}/48/*)

********************************** */

qx.Class.define("frontend.app.Toolbar",
{
    extend : qx.ui.toolbar.ToolBar,

    construct : function()
    {
        this.base(arguments);

        var calendar    = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:calendar"), "icon/48/mimetypes/office-calendar.png");
        var trainings   = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:trainings"), "icon/48/apps/preferences-accessibility.png");
        var documents   = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:documents"), "icon/48/mimetypes/office-document.png");
        var projects    = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:projects"), "icon/48/status/dialog-warning.png");
        var members     = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:members"), "icon/48/apps/preferences-users.png");
        var prints      = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:prints"), "icon/48/status/dialog-warning.png");
        var surveys     = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:surveys"), "icon/48/status/dialog-warning.png");
        var mailbox     = new qx.ui.toolbar.RadioButton(Tools.tr("toolbar:mailbox"), "icon/48/apps/internet-mail.png");

        mailbox.addListener("execute", function(e){
            qx.core.Init.getApplication().setContent("Mailbox");
        }, this);

        this.add(calendar);
        this.add(trainings);
        this.add(documents);
        this.add(projects);
        this.add(members);
        this.add(prints);
        this.add(surveys);
        this.add(mailbox);

        var radioGroup = new qx.ui.form.RadioGroup(calendar, trainings, documents, projects, members, prints, surveys, mailbox);

        this.setSpacing(25);
    }
})