/* *********************************

#asset(qx/icon/${qx.icontheme}/32/emblems/emblem-important.png)

********************************** */

qx.Class.define("frontend.lib.dialog.Error",
{
    extend : frontend.lib.dialog.Dialog,

    construct : function(message, icon)
    {
        this.base(arguments, Tools['tr'](message), icon || "icon/48/emblems/emblem-important.png");
        this.setCaption(Tools["tr"]("error occured"));
        var btnClose = new frontend.lib.ui.form.Button(Tools['tr']('close'), "button-close").set({
            allowGrowX : false,
            alignX : "center"
        });
        btnClose.addListener('execute', this.close, this);
        this.add(btnClose);

        this.set({
            showClose  : true,
            allowClose : true,
            maxWidth   : 1200
        });
    }
});