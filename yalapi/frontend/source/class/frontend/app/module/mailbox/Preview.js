qx.Class.define("frontend.app.module.mailbox.Preview",
{
    extend : qx.ui.container.Composite,

    events :
    {
        "changeMessageId" : "qx.event.type.Data",
        "changeMessage"   : "qx.event.type.Data"
    },

    properties :
    {
        messageId :
        {
            check : "Integer",
            init : null,
            nullable : true,
            apply : "_applyMessageId",
            event : "changeMessageId"
        },

        message :
        {
            check : "Map",
            init : null,
            nullable : true,
            event : "changeMessage"
        }
    },

    construct : function()
    {
        this.base(arguments);

        var layout = new qx.ui.layout.Grid(5, 5);
        this.setLayout(layout);
        this.setPadding(10);

        this.add(this.getChildControl("sender-label"), { row : 0, column : 0 });
        this.add(this.getChildControl("sender-value"), { row : 0, column : 1 });

        this.add(this.getChildControl("date-label"), { row : 1, column : 0 });
        this.add(this.getChildControl("date-value"), { row : 1, column : 1 });

        this.add(this.getChildControl("subject-label"), { row : 2, column : 0 });
        this.add(this.getChildControl("subject-value"), { row : 2, column : 1 });

        this.add(this.getChildControl("body-value"), { row : 3, column : 0, colSpan : 2 });

        var attachmentsLabel = this.getChildControl("attachments-label");
        var attachmentsValue = this.getChildControl("attachments-value");

        this.add(attachmentsLabel, { row : 4, column : 0 });
        this.add(attachmentsValue, { row : 4, column : 1 });

        layout.setColumnFlex(1, 1);
        layout.setRowFlex(3, 1);

        this.__cachedMessages = {};

        this.__request = new frontend.lib.io.HttpRequest;
        this.__request.addListener("success", this._onRequestSuccess, this);

        this.bind("message", this, "visibility", {
            converter : function(value) {
                return !!value ? "visible" : "excluded";
            }
        });

        attachmentsValue.bind("visibility", attachmentsLabel, "visibility");
    },

    members :
    {
        __cachedMessages : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "sender-label":
                    control = new qx.ui.basic.Label("Od:");
                    break;

                case "date-label":
                    control = new qx.ui.basic.Label("Data:");
                    break;

                case "subject-label":
                    control = new qx.ui.basic.Label("Temat:");
                    break;

                case "attachments-label":
                    control = new qx.ui.basic.Label("Załącznik:");
                    break;

                case "body-value":
                    control = new qx.ui.embed.Html().set({
                        minHeight : 200,
                        padding   : 2,
                        overflowY : "auto",
                        decorator : "main"
                    });
                    this.__bindToMessage(control, "body", "html");
                    break;

                case "date-value":
                    control = new qx.ui.basic.Label();
                    this.__bindToMessage(control, "send_date");
                    break;
                
                case "sender-value":
                    control = new qx.ui.basic.Label();
                    this.__bindToMessage(control, "sender");
                    break;
                
                case "subject-value":
                    control = new qx.ui.basic.Label();
                    this.__bindToMessage(control, "subject");
                    break;

                case "attachments-value":
                    control = new frontend.app.module.mailbox.preview.Attachments();
                    this.__bindToMessage(control, "attachments");
                    break;
            }

            return control || this.base(arguments, id);
        },

        __bindToMessage : function(control, sourceProperty, targetProperty)
        {
            this.bind("message", control, targetProperty || "value", {
                converter : function(value) {
                    if (value) {
                        return value[sourceProperty];
                    }
                    return null;
                }
            });
        },

        _onRequestSuccess : function(e)
        {
            var message = this.__request.getResponseJson();
            if (message) {
                this.setMessage(this.__cachedMessages[message.id] = message);
            }
        },

        _applyMessageId : function(value, old)
        {
            this.setMessage(null);

            if (value) {
                if (this.getCachedMessage(value) != null) {
                    this.setMessage(this.__cachedMessages[value]);
                } else {
                    this.__request.setUrl(Urls.resolve("MESSAGES", value));
                    this.__request.send();
                }
            }
        },

        getCachedMessage : function(messageID)
        {
            return this.__cachedMessages[messageID] || null;
        }
    }
});