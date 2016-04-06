/*
#ignore(CKEDITOR)
 */

qx.Class.define("frontend.lib.ui.form.CKEditor",
{
    extend : qx.ui.container.Composite,

    implement : [
        qx.ui.form.IStringForm,
        qx.ui.form.IForm
    ],

    include : [
        qx.ui.form.MForm
    ],

    events :
    {
        "changeValue" : "qx.event.type.Data"
    },

    properties :
    {
        toolbar :
        {
            check : [ "simple", "advanced" ],
            init : "simple"
        }
    },

    construct : function()
    {
        this.base(arguments, new qx.ui.layout.VBox);

        this.setMinWidth(600);
        this.setMinHeight(300);

        this.addListenerOnce("appear", function(e){
            var bounds  = this.getBounds();
            var toolbar = this.getToolbar();

            this.__config.toolbar = qx.lang.String.firstUp(toolbar);
            this.__config.height  = bounds.height - 70;
            
            this.__ckEditor = CKEDITOR.replace(this.getContentElement().getDomElement(), this.__config);
            this.__ckEditor.on("blur", function(e){
                var data = e.editor.getData();
                this.setValue(data);
            }, this);
            this.addListener("resize", this._onResize, this);
        }, this);
    },

    members :
    {
        __config :
        {
            language : "pl",
            toolbar : "Advanced",
            toolbar_Simple :
            [
                ["Format", "Font", "FontSize", "Bold", "Italic", "Underline", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "-", "Source"]
            ],
            toolbar_Advanced :
            [
                ["Format", "Font", "FontSize", "TextColor", "BGColor", "-", "Bold", "Italic", "Underline", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "-", "NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "Source"]
            ],
            resize_enabled : false
        },

        __ckEditor : null,

        __value : "",

        _onResize : function()
        {
            try {
                var bounds = this.getBounds();
                this.__ckEditor.resize(bounds.width, bounds.height);
            } catch (ex) {

            }
        },

        setValue : function(value)
        {
            if (this.__ckEditor) {
                var old = this.__ckEditor.getData();
                this.__ckEditor.setData(value);
                this.fireDataEvent("changeValue", value, old);
            } else if (value) {
                this.addListenerOnce("appear", function(){
                    this.setValue(value);
                }, this);
            }

            this.__value = value;
        },

        getValue : function()
        {
            if (this.__ckEditor) {
                return this.__ckEditor.getData();
            }
            return this.__value;
        },

        resetValue : function()
        {
            this.setValue("");
        }
    }
});