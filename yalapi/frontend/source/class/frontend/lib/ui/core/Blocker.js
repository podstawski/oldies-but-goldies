qx.Class.define("frontend.lib.ui.core.Blocker",
{
    extend : qx.ui.core.Blocker,

    events :
    {
        "block" : "qx.event.type.Event",
        "unblock" : "qx.event.type.Event"
    },

    properties :
    {
        opacity :
        {
            refine : true,
            init : 0.4
        },

        color :
        {
            refine : true,
            init : "modal-background"
        }
    },

    members :
    {
        // SIM overriden
        block : function()
        {
            this.__blockerCount++;
            if (this.__blockerCount < 2)
            {
                this._backupActiveWidget();

                var blocker = this.getBlockerElement();
                blocker.include();
                blocker.activate();

                blocker.addListener("deactivate", this.__activateBlockerElement, this);
                blocker.addListener("keypress", this.__stopTabEvent, this);
                blocker.addListener("keydown", this.__stopTabEvent, this);
                blocker.addListener("keyup", this.__stopTabEvent, this);

                this.fireEvent("block");
            }
        },

        // SIM overriden
        unblock : function()
        {
            if (!this.isBlocked()){
                return;
            }

            this.__blockerCount--;
            if (this.__blockerCount < 1) {
                this.__unblock();
                this.__blockerCount = 0;

                this.fireEvent("unblock");
            }
        }
    }
});