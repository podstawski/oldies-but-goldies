qx.Class.define("frontend.app.module.wizard.Abstract",
{
    extend : qx.ui.container.Composite,

    events :
    {
        "changeSteps" : "qx.event.type.Data",
        "changeCurrentStep" : "qx.event.type.Data",
        "completed" : "qx.event.type.Event"
    },

    properties :
    {
        currentStep :
        {
            check : "Integer",
            init : 1,
            event : "changeCurrentStep",
            apply : "_applyCurrentStep"
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.HBox(10, "center"));
        
        if (this._steps) {
            var steps = qx.lang.Object.getValues(this._steps);
            var control;
            for (var i = 1, L = qx.lang.Object.getLength(this._steps); i <= L; i++) {
                control = this.getChildControl("step#" + i);
                control.setTitle(i);
                control.setLabel(steps[i - 1]);
                this.add(control);
                if (i < L) {
                    this.add(this.getChildControl("arrow#" + i));
                }
            }
            this.initCurrentStep();
            this.addListenerOnce("completed", this._onComplete, this);
        }
    },

    members :
    {
        __currentListener : null,

        _steps : null,

        _applyCurrentStep : function(value, old)
        {
            var step = 1;
            var control;

            if (this.__currentListener != null) {
                this.__currentListener[0].removeListenerById(this.__currentListener[1]);
            }

            while ((control = this.getChildControl("step#" + step, true)) != null) {
                control.setCursor(null);
                if (step < value) {
                    control.addState("ok");
                } else if (step == value) {
                    this.__currentListener = [
                        control,
                        control.addListener("click", this._onStepClick, this)
                    ];
                    control.setCursor("pointer");
                } else {

                }
                step++;
            }

            if (step == value) {
                this.fireEvent("completed");
            }
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "arrow":
                    control = new qx.ui.basic.Image("wizard-arrow");
                    control.setMarginTop(20);
                    break;

                case "step":
                    control = new frontend.app.module.wizard.Step();
                    break;
            }

            return control || this.base(arguments, id);
        },

        _onStepClick : function(e)
        {
            var currentStep = this.getCurrentStep();
            var steps = qx.lang.Object.getKeys(this._steps);
            if (steps[currentStep - 1]) {
                var form, eventName = "close", clazz = qx.Class.getByName(steps[currentStep - 1]);
                try {
                    form = new clazz();
                } catch (e) {
                    form = clazz.getInstance();
                }
                if (qx.Class.supportsEvent(clazz, "completed")) {
                    eventName = "completed";
                }
                form.addListenerOnce(eventName, function(e) {
                    this.setCurrentStep(currentStep + 1);
                }, this);
                form.open();
            }
        },

        _onComplete : function(e)
        {
            var request = new frontend.lib.io.HttpRequest;
            request.setUrl(Urls.resolve("DASHBOARD", {
                complete : this.classname.split(".").pop()
            }));
            request.send();
        }
    }
});