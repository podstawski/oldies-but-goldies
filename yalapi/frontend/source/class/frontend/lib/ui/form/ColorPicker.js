qx.Class.define("frontend.lib.ui.form.ColorPicker",
{
    extend : qx.ui.basic.Label,

    implement : [
        qx.ui.form.IStringForm,
        qx.ui.form.IForm
    ],

    include : [
        qx.ui.form.MForm
    ],

    construct : function(boxSize)
    {
        this.base(arguments);

        if (boxSize) {
            this.setBoxSize(boxSize);
        } else {
            this.initBoxSize();
        }

        this.addListenerOnce("click", this._createPopup, this);
        this.addListener("click", this._onClick, this);
    },

    properties :
    {
        appearance :
        {
            refine : true,
            init : "color-picker"
        },

        boxSize :
        {
            check : "Integer",
            nullable : true,
            init : 20,
            apply : "_applyBoxSize"
        },

        position :
        {
            check :
            [
                "top-left", "top-right",
                "bottom-left", "bottom-right",
                "left-top", "left-bottom",
                "right-top", "right-bottom"
            ],
            init : "bottom-left",
            apply : "_applyPosition"
        }
    },

    members :
    {
        __popup : null,
        __colorSelector : null,

        // SIM overriden
        _applyValue : function(value, old)
        {
            if (value) {
                this.setBackgroundColor(value);
                this.setToolTipText(value);
            }
        },

        _applyBoxSize : function(value)
        {
            this.set({
                width   : value,
                height  : value
            });
        },

        _applyPosition : function(value, old)
        {
            if (this.__popup)
                this.__popup.setPosition(value);
        },

        _createPopup : function()
        {
            if (this.__popup === null)
            {
                this.__popup = new qx.ui.popup.Popup(new qx.ui.layout.VBox).set({
                    padding : 10
                });
                this.__popup.placeToWidget(this);
                this.__popup.setPosition(this.getPosition());

                this.__colorSelector = new frontend.lib.ui.control.ColorSelector();

                var buttonBar = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "right")).set({marginTop: 10});
                var buttonOk = new qx.ui.form.Button("OK");
                var buttonCancel = new qx.ui.form.Button("Anuluj");
                buttonOk.addListener("execute", this._onColorSelectorOk, this);
                buttonCancel.addListener("execute", this._onColorSelectorCancel, this);
                buttonBar.add(buttonCancel);
                buttonBar.add(buttonOk);

                this.__popup.add(this.__colorSelector);
                this.__popup.add(buttonBar);
            }
        },

        _onClick : function(e)
        {
            this.__popup.show();
        },

        _onColorSelectorOk : function()
        {
            var rgb = [
                this.__colorSelector.getRed(),
                this.__colorSelector.getGreen(),
                this.__colorSelector.getBlue()
            ];
            var color = "#" + qx.util.ColorUtil.rgbToHexString(rgb);
            this.setValue(color);
            this.__popup.hide();
        },
        
        _onColorSelectorCancel : function()
        {
            this.__popup.hide();
        }
    }
});