qx.Class.define("frontend.app.module.user.NewStudent",
{
    extend : frontend.lib.ui.window.Modal,

    events :
    {
        "completed" : "qx.event.type.Event"
    },

    construct : function()
    {
        this.base(arguments);

        var blocker = qx.core.Init.getApplication().getRoot().getBlocker();
        blocker.block();

        var win = window.open(Urls.resolve("REPORTS", {
            id : 10,
            report_format : "pdf",
            user_id : frontend.app.Login.getId()
        })
        );
//        , null, "width=100,height=100,top=100,left=100,location=no,menubar=no,status=no");
        
        var timer = new qx.event.Timer(100);
        timer.addListener("interval", function(e) {
            if (win.closed) {
                blocker.unblock();
                timer.stop();
                this.open(true);
            }
        }, this);
        timer.start();

        var button = new qx.ui.form.Button("Tak, wydrukowałem kartę zgłoszeniową");
        button.addListener("execute", function(e){
            this.fireEvent("completed");
            this.close();
        }, this);
        this.add(button);
    },

    members :
    {
        open : function(flag)
        {
            if (flag) {
                this.base(arguments);
            }
        }
    }
});