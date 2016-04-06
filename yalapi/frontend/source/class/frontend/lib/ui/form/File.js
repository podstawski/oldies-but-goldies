qx.Class.define("frontend.lib.ui.form.File",
{
    extend : qx.ui.container.Composite,

    implement : [
        qx.ui.form.IForm,
        frontend.lib.ui.form.IFile
    ],

    include : [
        qx.ui.form.MForm
    ],

    properties :
    {

    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.HBox(2));

        this.add(this._createChildControl("textfield"), {flex: 1});
        this.add(this._createChildControl("browsebutton"));

        this.setRequiredInvalidMessage("Proszę wybrać plik");

        this.addListener("resize", this._onResize, this);
        this.addListener("changeValid", this._onChangeValid, this);
    },

    members :
    {
        _applyEnabled : function(enabled, old)
        {
            var input = this.getChildControl("hiddeninput", true);
            if (input) {
                input.disabled = !enabled;
            }
        },
        
        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "textfield":
                    control = new frontend.lib.ui.form.TextField().set({
                        alignY : "middle"
                    });
                    break;

                case "browsebutton":
                    control = new frontend.lib.ui.form.Button("Przeglądaj").set({
                        alignY : "middle"
                    });
                    break;

                case "hiddeninput":
                    control = document.createElement("input");
                    control.type = "file";
                    control.style.position  = "absolute";
                    control.style.left      = "0px";
                    control.style.top       = "0px";
                    control.style.zIndex    = "100";
                    control.style.cursor    = "hand";
                    control.style.cursor    = "pointer";
                    control.style.filter    = "alpha(opacity=0)";
                    control.style.opacity   = "0";
                    control.style.MozOutlinestyle = "none";
                    control.style.hidefocus = "true";

                    control.onchange = qx.lang.Function.bind(this._onChange(control), this);
                    break;
            }

            return control || this.base(arguments, id);
        },

        _onChange : function(input)
        {
            var textfield = this.getChildControl("textfield");
            return function(e)
            {
                textfield.setValue(input.value);
                if (textfield.getValid() === false) {
                    textfield.setValid(true);
                }
            }
        },

        _onResize : function(e)
        {
            var data  = e.getData();
            var input = this.getChildControl("hiddeninput", true);
            if (input) {
                input.style.left   = data.left + "px";
                input.style.top    = data.top + "px";
                input.style.width  = data.width + "px";
                input.style.height = data.height + "px";
            }
        },

        _onChangeValid : function(e)
        {
            var valid = e.getData();
            this.getChildControl("textfield").setValid(valid);
        },

        getFileName : function()
        {
            var input = this.getChildControl("hiddeninput", true);
            if (input) {
                return input.value;
            }
            return null;
        }
    }
});