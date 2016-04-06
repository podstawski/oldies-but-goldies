qx.Class.define("frontend.app.module.group.GoogleIdPrompt",
{
    extend : frontend.lib.form.Abstract,

    construct : function(rowData)
    {
        this.base(arguments, rowData);
        this.setCaption("");

        var form = this.getForm();
        form._add(new qx.ui.basic.Label("@" + frontend.app.Login.getDomain()), { row: 0, column : 2 });
        form.getLayout().setColumnAlign(2, "left", "middle");

        this.addListenerOnce("appear", function(e){
            var element = form._buttonRow.getContainerElement();
            element.removeStyle("left");
            element.setStyle("right", "1px");
        }, this);
    },

    members :
    {
        _url      : "GROUPS",
        _prefix   : "group.manager",
        _template :
        {
            google_group_id : {
                type : "TextField",
                properties : {
                    required : true
                },
                validators : [
                    Validate.string(),
                    Validate.slength(0, 256),
                    Validate.regex(/^[a-zA-z0-9-_\.]+$/, Tools.tr("group.manager.error:invalid_google_group_id"))
                ]
            }
        },

        _onComplete : function()
        {
            this.close();
        }
    }
});