qx.Class.define("frontend.app.module.group.Main",
{
    extend : qx.ui.container.Composite,

    construct : function()
    {
        this.base(arguments);

        this.setLayout( new qx.ui.layout.Basic() );

        var button = new qx.ui.form.Button("Dodaj szkolenie");
        button.addListener("execute", function(){
            var form = new frontend.app.form.group.Add();
            form.open();
        }, this);

        this.add(button);
    },

    members :
    {

    }
})