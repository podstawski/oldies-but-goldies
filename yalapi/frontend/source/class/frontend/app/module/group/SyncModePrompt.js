qx.Class.define("frontend.app.module.group.SyncModePrompt",
{
    extend : frontend.lib.ui.window.Modal,

    include  : [
        frontend.MMessage
    ],

    events :
    {
        "groupSynced" : "qx.event.type.Event"
    },

    construct : function()
    {
        this.base(arguments);

        this.setCaption(Tools["tr"]("group.sync:window caption"));
        this.setLayout(new qx.ui.layout.HBox(10, "center"));

        this.add(this.getChildControl("mode-button#export"));
        this.add(this.getChildControl("mode-button#import"));
        this.add(this.getChildControl("mode-button#merge"));

        this.__syncRequest = new frontend.lib.io.HttpRequest;
        this.__syncRequest.addListener("success", this._onSyncRequestSuccess, this);
    },

    members :
    {
        __syncRequest : null,

        __groupID : null,

        _createChildControlImpl : function(id, syncMode)
        {
            var control;

            switch (id)
            {
                case "mode-button":
                    control = new frontend.lib.ui.form.Button(Tools["tr"]("group.sync.mode:" + syncMode));
                    control.setWidth(100);
                    var tooltip = new qx.ui.tooltip.ToolTip();
                    tooltip.setShowTimeout(0);
                    tooltip.setPlaceMethod("widget");
                    tooltip.setPosition("bottom-left");
                    tooltip.add(new qx.ui.basic.Label(Tools["tr"]("group.sync.mode.tooltip:" + syncMode)));
//                    control.setToolTip(tooltip);
                    control.addListener("execute", this._onModeButtonClick(syncMode), this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        _onModeButtonClick : function(syncMode)
        {
            return function(e)
            {
                this.__syncRequest.setUrl(Urls.resolve("GOOGLE_APPS_SYNC_GROUP", {
                    group_id  : this.__groupID,
                    sync_mode : syncMode
                }));
                this.__syncRequest.send();
            }
        },

        _onSyncRequestSuccess : function(e)
        {
            this.showMessage("group.sync:success");
            this.fireEvent("groupSynced");
            this.close();
        },

        open : function(groupID)
        {
            if (this.__groupID = groupID) {
                this.base(arguments);
            } else {
                this.showError("group.sync:no group selected");
            }
        }
    }
});