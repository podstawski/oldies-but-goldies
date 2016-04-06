/* *********************************

#asset(qx/icon/${qx.icontheme}/32/status/dialog-information.png)

********************************** */

qx.Class.define("frontend.lib.dialog.Dialog",
{
    extend : qx.ui.window.Window,

    properties :
    {
        message :
        {
            check : "String",
            apply : "_applyMessage"
        }
    },

    construct : function(message, icon)
    {
        this.base(arguments);

        var layout = new qx.ui.layout.VBox(10);
        this.setLayout(layout);
        
        this.set({
            modal : true,
            resizable : false,
            showMinimize : false,
            allowMinimize : false,
            showClose : false,
            allowClose : false,
            showMaximize : false,
            allowMaximize : false
        });

        this._atom = new qx.ui.basic.Atom(null, icon || "icon/48/status/dialog-information.png").set({
            font: qx.bom.Font.fromString("16px"),
            rich: true,
//            textAlign: "center",
            padding: 10
        });

        this._atom.getChildControl("label").setPaddingLeft(10);

        if (message) {
            this.setMessage(message);
        }

        this.add(this._atom);

        this.center();
        this.open();
    },

    members :
    {
        _atom : null,
        _applyMessage : function(label)
        {
            this._atom.setLabel(label);
        }
    }
})