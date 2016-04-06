qx.Class.define("frontend.app.module.wizard.Step",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        title :
        {
            check : "Integer",
            apply : "_applyValue"
        },

        label :
        {
            check : "String",
            apply : "_applyValue"
        }
    },

    construct : function()
    {
        this.base(arguments);
        this.setAppearance("wizard-step");
        
        this.setLayout(new qx.ui.layout.VBox(10, "top"));

        this.add(this.getChildControl("title"));
        this.add(this.getChildControl("label"));
    },

    members :
    {
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id) {
                case "title":
                    control = new qx.ui.basic.Label();
                    break;

                case "label":
                    control = new qx.ui.basic.Label();
                    control.setRich(true);
                    break;
            }
            return control || this.base(arguments, id, hash);
        },

        _forwardStates :
        {
            ok : true
        },

        _applyValue : function(value, old, name)
        {
            this.getChildControl(name, true).setValue(new String(value));
        }
    }
});