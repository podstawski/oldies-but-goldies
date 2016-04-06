/* *********************************

#ignore(tinyMCE.Editor)

********************************** */

qx.Class.define("frontend.lib.ui.form.TinyMCE",
{
    extend : qx.ui.form.TextArea,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "tinymce"
        },

        autoSize :
        {
            refine : true,
            init : true
        }
    },

    construct : function(value)
    {
        this.base(arguments, value);

        this.addListenerOnce("appear", this._initTinyMCE, this);
        
        this.addListener("resize", this._resizeIframe, this);

        this.addListener("changeEnabled", this._onChangeEnabled, this);
    },

    members :
    {
        _editorInstance : null,
        
        _editorOptions :
        {
            mode : "textareas",
            theme : "advanced",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,ustifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,|,blockquote",
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            width : "100%",
            height : "100%",
            content_css : "script/tiny_mce/content.css"
        },

        _initTinyMCE : function()
        {
            this._editorId = "tinymce_" + new Date().getTime();
            this.getContentElement().getDomElement().id = this._editorId;
            this._editorInstance = new tinyMCE.Editor(this._editorId, this._editorOptions);
            this._editorInstance.render();

            qx.event.Timer.once(this._resizeIframe, this, 100);
        },

        _resizeIframe : function()
        {
            var mceiframe = document.getElementById(this._editorId + "_ifr");
            if (mceiframe) {
                qx.event.Timer.once(function(){
                    mceiframe.style.height = (parseInt(document.getElementById(this._editorId).parentNode.style.height, 10) - 28) + "px";
                }, this, 0);
            }
        },

        getValue : function()
        {
            var value = null;
            try {
                value = this._editorInstance.getContent();
            }
            catch (ex) {}
            
            return value;
        },

        _onChangeEnabled : function(e)
        {
            // SIM I dont know how to disable TinyMCE... :/
        }
    }
});