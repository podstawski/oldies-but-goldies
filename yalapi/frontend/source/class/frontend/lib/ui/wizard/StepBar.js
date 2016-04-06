qx.Class.define("frontend.lib.ui.wizard.StepBar",
{
    extend : qx.ui.container.Composite,

    construct : function(title)
    {
        this.base(arguments);
        
        this.setLayout(new qx.ui.layout.HBox);

        this.setAppearance("wizard-step-bar");

        var label = new qx.ui.basic.Label(title);
        label.setAppearance("wizard-step-label");

        this.add(label, {flex: 1});
    },

    members :
    {
        setState : function(state)
        {
            ["previous", "current", "next"].forEach(function(key){
                this.removeState(key);
            }, this);
            this.addState(state);
        }
    }
});