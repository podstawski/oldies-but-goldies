qx.Class.define("frontend.lib.ui.wizard.Wizard",
{
    extend : qx.ui.container.Composite,

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox(20));

        this._progress  = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
        this._stack     = new frontend.lib.ui.container.Stack().set({rotationEnabled : false});
        this._buttons   = new qx.ui.container.Composite(new qx.ui.layout.HBox(10).set({alignX: "center"})).set({padding: 10});
        
        this.add(this._progress);
        this.add(this._stack);
        this.add(this._buttons);

        this._stack.addListener("changeSelection", this._onChangeSelection, this);
        this._stack.addListener("changeSelection", this._updateButtons, this);

        this._makeButtons();
    },

    events :
    {
        "changePrevLabel" : "qx.event.type.Data",
        "changeNextLabel" : "qx.event.type.Data",
        "changeFinishLabel" : "qx.event.type.Data"
    },

    properties :
    {
        callback :
        {
            check : "Function",
            apply : "_applyCallback"
        },

        finishLabel :
        {
            check : "String",
            init : "FINISH",
            event : "changeFinishLabel"
        },

        nextLabel :
        {
            check : "String",
            init : "NEXT",
            event : "changeNextLabel"
        },

        prevLabel :
        {
            check : "String",
            init : "PREV",
            event : "changePrevLabel"
        }
    },

    members :
    {
        _stack : null,

        _progress : null,

        _buttons : null,

        addStep : function(title, form)
        {
//            qx.core.Assert.assertTrue(qx.Class.isSubClassOf(step.constructor, frontend.lib.ui.wizard.Step));
            
            var stepBar = new frontend.lib.ui.wizard.StepBar(title);
            if (!this._progress.hasChildren()) {
                stepBar.addState("first");
                stepBar.addState("current");
            } else {
                stepBar.addState("next");
            }

            this._progress.add(stepBar, {flex: 1});
            this._stack.add(form);

            this._updateButtons();
        },

        _makeButtons : function()
        {
            if (this._buttons.hasChildren()) {
                return;
            }
            
            var previous = new qx.ui.form.Button().set({appearance: "wizard-button-previous"});
            previous.addListener("execute", this._onPrevClick, this);

            var next = new qx.ui.form.Button().set({appearance: "wizard-button-next"});
            next.addListener("execute", this._onNextClick, this);

            this.bind("nextLabel", next, "label");
            this.bind("prevLabel", previous, "label");

            this._buttons.add(previous);
            this._buttons.add(next);

            this._buttons.setUserData("previous", previous);
            this._buttons.setUserData("next", next);
        },

        _applyCallback : function(callback, old)
        {

        },

        _onNextClick : function()
        {
            var form = this._stack.getSelection()[0];

            if (this._stack.isNext()) {
                form.addListenerOnce("completed", this._stack.next, this._stack);
            } else if (this.getCallback()) {
                form.addListenerOnce("completed", this.getCallback(), this);
            }
            form.send();
        },

        _onPrevClick : function()
        {
            this._stack.previous();
        },

        getValues : function()
        {
            var values = {};
            this._stack.getChildren().forEach(function(form){
                values = qx.lang.Object.carefullyMergeWith(values, form.getValues());
            }, this);
            return values;
        },

        _onChangeSelection : function(e)
        {
            var selected = this._stack.getSelection()[0];
            var selectedIndex = this._stack.indexOf(selected);
            
            this._progress.getChildren().forEach(function(stepbar, index){
                if (selectedIndex - 1 == index) {
                    stepbar.setState("previous");
                } else if (selectedIndex == index) {
                    stepbar.setState("current");
                } else if (selectedIndex + 1 == index) {
                    stepbar.setState("next");
                }
            }, this);
        },
        
        _updateButtons : function()
        {
            var selected = this._stack.getSelection()[0];
            var selectedIndex = this._stack.indexOf(selected);

            var next = this._buttons.getUserData("next");
            var prev = this._buttons.getUserData("previous");

            if (selectedIndex == 0) {
                prev.setVisibility("hidden");
            } else {
                prev.setVisibility("visible");
            }

//            if (selectedIndex == this._progress.getChildren().length - 1) {
//                next.setVisibility("excluded");
//            } else {
//                next.setVisibility("visible");
//            }
        }
    }
});