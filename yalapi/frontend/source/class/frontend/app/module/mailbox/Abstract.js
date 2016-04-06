qx.Class.define("frontend.app.module.mailbox.Abstract",
{
    type : "abstract",
    
    extend : qx.ui.container.Composite,

    include : [
        frontend.MMessage
    ],

    properties :
    {
        appearance :
        {
            refine : true,
            init : "ui-table-list"
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox(10));

        this._folder = this.self(arguments)["FOLDER_" + this.classname.split(".").pop().toUpperCase()];

        var toolbar = this.getChildControl("toolbar");
        toolbar.getChildControl("compose-button").set({
            enabled : true
        }).addListener("execute", this._onComposeClick, this);
        this.add(toolbar);

        var bindOptions = { converter : function(value) { return !!value; } };
        var table       = this.getChildControl("table");
        var deleteBtn   = toolbar.getChildControl("delete-button");
        var replyBtn    = toolbar.getChildControl("reply-button");
        var forwardBtn  = toolbar.getChildControl("forward-button");
        
        table.bind("changeRowSelectedCount", deleteBtn,  "enabled", bindOptions);
        table.bind("changeSelectedCount",    replyBtn,   "enabled", bindOptions);
        table.bind("changeSelectedCount",    forwardBtn, "enabled", bindOptions);

        table.addListener("cellDblclick", this._onTableCellDblclick, this);

        deleteBtn.addListener("execute",  this._onDeleteClick,  this);
        replyBtn.addListener("execute",   this._onReplyClick,   this);
        forwardBtn.addListener("execute", this._onForwardClick, this);

        var tableSelectionModel = table.getSelectionModel();
        tableSelectionModel.addListener("changeSelection", this._onTableChangeSelection, this);

        var preview = this.getChildControl("preview");
        var window  = this.getChildControl("window");
        preview.bind("message", window, "message");
        window.addListenerOnce("appear", function(e){
            window.bind("visibility", preview, "visibility", {
                converter : function(value) {
                    if (value == "visible") {
                        return "excluded";
                    }
                    return "visible";
                }
            });
        }, this);

        var splitpane = new qx.ui.splitpane.Pane("vertical");
        splitpane.add(table, 1);
        splitpane.add(preview, 0);
        this.add(splitpane, {flex:1});
    },

    members :
    {
        _folder : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "toolbar":
                    control = new frontend.app.module.mailbox.Toolbar;
                    break;

                case "table":
                    control = new frontend.app.module.mailbox.Table(this._folder);
                    break;

                case "preview":
                    control = new frontend.app.module.mailbox.Preview;
                    break;

                case "window":
                    control = new frontend.app.module.mailbox.preview.Window;
                    break;
            }

            return control || this.base(arguments, id);
        },

        _openCompose : function()
        {
            var win = new frontend.app.module.mailbox.Compose;
            var tableModel = this.getChildControl("table").getTableModel();
            win.addListenerOnce("messageSent", function(e){
                tableModel.reloadData();
                win.close();
            }, this);
            win.open();
            return win;
        },

        _onComposeClick : function(e)
        {
            this._openCompose();
        },

        _onTableChangeSelection : function(e)
        {
            var messageID       = null;
            var selectedRowData = this.getChildControl("table").getSelectedRowData();
            if (selectedRowData) {
                messageID = selectedRowData.message_id;
            }
            this.getChildControl("preview").setMessageId(messageID);
        },

        _onDeleteClick : function(e)
        {
            var table             = this.getChildControl("table");
            var selectedRowsCount = table.getSelectedRowCount();

            if (selectedRowsCount > 0)
            {
                var dialog = new frontend.lib.dialog.Confirm("Na pewno chcesz usunąć zaznaczone wiadomości?");
                dialog.addListenerOnce("yes", function(e) {
                    var loading = new frontend.lib.dialog.Dialog("Proszę czekać...");
                    var request = new frontend.lib.io.HttpRequest().set({
                        url : Urls.resolve("MESSAGES", {
                            id      : table.getSelectedRowsIds().toString(),
                            folder  : this._folder
                        }),
                        method              : "DELETE",
                        showLoadingDialog   : false
                    });
                    request.addListenerOnce("success", function(e){
                        table.getSelectionModel().resetSelection();
                        table.resetSelectedRows();
                        table.getTableModel().reloadData();

                        loading.close();
                        
                        this.showMessage("Wiadomości zostały usunięte!");
                    }, this);
                    request.send();
                }, this);
            }
        },

        _onReplyClick : function(e)
        {
            var message = this.getChildControl("preview").getMessage();
            if (message) {
                this._openCompose().changeRecipient("users", {
                    id      : message.sender_id,
                    sender  : message.sender
                }).getForm().populate({
                    subject : "RE: " + message.subject,
                    body    : this._reformatMessageBody(message.body)
                });
            }
        },

        _onForwardClick : function(e)
        {
            var message = this.getChildControl("preview").getMessage();
            if (message) {
                var form = this._openCompose().getForm().populate({
                    subject : "FWD: " + message.subject,
                    body    : this._reformatMessageBody(message.body)
                });
                form.createHiddenInput("_forward", message.id);
                if (message.attachments) {
                    var preview = new frontend.app.module.mailbox.preview.Attachments();
                    preview.setValue(message.attachments);
                    form._add(preview, { row : 4, column : 0, colSpan : 3 });
                }
            }
        },

        _reformatMessageBody : function(body)
        {
            return body;
            if (body) {
                return ">> " + body.replace("<p>", "<p>>> ");
            }
            return null;
        },

        __window : null,

        _onTableCellDblclick : function(e)
        {
            this.getChildControl("window").open();
        }
    },

    statics :
    {
        FOLDER_INBOX    : 1,
        FOLDER_OUTBOX   : 2,
        FOLDER_TRASH    : 3
    }
});