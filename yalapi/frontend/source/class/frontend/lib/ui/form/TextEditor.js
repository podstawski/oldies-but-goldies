/* ************************************************************************

#asset(qx/icon/${qx.icontheme}/16/actions/format-*.png)
#asset(qx/icon/${qx.icontheme}/16/actions/edit-*.png)
#asset(qx/icon/${qx.icontheme}/16/actions/insert-image.png)
#asset(qx/icon/${qx.icontheme}/16/actions/insert-link.png)
#asset(qx/icon/${qx.icontheme}/16/actions/insert-text.png)

#asset(frontend/icons/texteditor/*)

************************************************************************ */

qx.Class.define("frontend.lib.ui.form.TextEditor",
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
        appearance :
        {
            refine : true,
            init : "text-editor"
        },

        mode :
        {
            check : [ "simple" , "advanced", "full" ],
            init : "simple",
            apply : "_applyMode"
        }
    },

    construct : function(mode)
    {
        this.base(arguments, new qx.ui.layout.VBox);

        this.add(this.getChildControl("toolbar"));
        this.add(this.getChildControl("htmlarea"), {flex:1});

        if (mode) {
            this.setMode(mode);
        } else {
            this.initMode();
        }

        this.setFocusable(true);
        this.setDecorator("main");
    },

    members :
    {
        __commands :
        {
            "fontFamily"          : { custom: "__fontFamilyToolbarEntry" },
            "fontSize"            : { custom: "__fontSizeToolbarEntry" },
            "bold"                : { image: "icon/16/actions/format-text-bold.png", action: "setBold" },
            "italic"              : { image: "icon/16/actions/format-text-italic.png", action: "setItalic" },
            "underline"           : { image: "icon/16/actions/format-text-underline.png", action: "setUnderline" },
            "strikethrough"       : { image: "icon/16/actions/format-text-strikethrough.png", action: "setStrikeThrough" },
            "removeFormat"        : { image: "icon/16/actions/edit-clear.png", action: "removeFormat" },
            "alignLeft"           : { image: "icon/16/actions/format-justify-left.png", action: "setJustifyLeft" },
            "alignCenter"         : { image: "icon/16/actions/format-justify-center.png", action: "setJustifyCenter" },
            "alignRight"          : { image: "icon/16/actions/format-justify-right.png", action: "setJustifyRight" },
            "alignJustify"        : { image: "icon/16/actions/format-justify-fill.png", action: "setJustifyFull" },
            "indent"              : { image: "icon/16/actions/format-indent-more.png", action: "insertIndent" },
            "outdent"             : { image: "icon/16/actions/format-indent-less.png", action: "insertOutdent" },
            "fontColor"           : { image: "frontend/icons/texteditor/format-text-color.png", action: "__fontColorHandler" },
            "textBackgroundColor" : { image: "frontend/icons/texteditor/format-fill-color.png", action: "__textBackgroundColorHandler" },
            "insertImage"         : { image: "icon/16/actions/insert-image.png", action: "__insertImageHandler" },
            "insertTable"         : { image: "frontend/icons/texteditor/insert-table.png", action: "__insertTableHandler" },
            "insertLink"          : { image: "icon/16/actions/insert-link.png", action: "__insertLinkHandler" },
            "insertHTML"          : { image: "frontend/icons/texteditor/insert-text.png", action: "__insertHTMLHandler" },
            "insertHR"            : { image: "frontend/icons/texteditor/insert-horizontal-rule.png", action: "insertHorizontalRuler" },
            "ol"                  : { image: "frontend/icons/texteditor/format-list-ordered.png", action: "insertOrderedList" },
            "ul"                  : { image: "frontend/icons/texteditor/format-list-unordered.png", action: "insertUnorderedList" },
            "undo"                : { image: "icon/16/actions/edit-undo.png", action: "undo" },
            "redo"                : { image: "icon/16/actions/edit-redo.png", action: "redo" }
        },

        __modes :
        {
            "simple"   : "fontFamily,fontSize|bold,italic,underline|alignLeft,alignCenter,alignRight,alignJustify",
            "advanced" : "fontFamily,fontSize,fontColor|bold,italic,underline,strikethrough|alignLeft,alignCenter,alignRight,alignJustify|indent,outdent|ol,ul|undo,redo",
            "full"     : "fontFamily,fontSize,fontColor|bold,italic,underline,strikethrough,removeFormat|alignLeft,alignCenter,alignRight,alignJustify|indent,outdent|ol,ul|textBackgroundColor,insertImage,insertTable,insertLink,insertHTML,insertHR|undo,redo"
        },

        __setupToolbar : function()
        {
            var toolbar = this.getChildControl("toolbar");
            if (toolbar.hasChildren()) {
                toolbar.removeAll();
            }

            var htmlarea = this.getChildControl("htmlarea");
            var toolbarEntries = this.__modes[this.getMode()].split("|");
            for (var i = 0, l = toolbarEntries.length; i < l; i++)
            {
//                if (i > 0) {
//                    toolbar.addSeparator();
//                }

                var part = new qx.ui.toolbar.Part();
                toolbar.add(part);

                var buttons = toolbarEntries[i].split(",");
                for (var k = 0, m = buttons.length; k < m; k++)
                {
                    var entry = buttons[k];
                    var info  = this.__commands[entry];
                    var button;
                    if (info.custom) {
                        button = this[info.custom].call(this);
                    } else {
                        button = new qx.ui.toolbar.Button(null, info.image).set({
                            focusable   : false,
                            keepFocus   : true,
                            center      : true,
                            toolTipText : info.text || Tools["tr"]("texteditor:" + entry)
                        });

                        button.addListener(
                            "execute",
                            typeof htmlarea[info.action] === "function"
                                 ? htmlarea[info.action]
                                 : (typeof this[info.action] === "function"
                                         ? this[info.action]
                                         : qx.lang.Function.empty
                                 ),
                            htmlarea
                        );
                    }
//                    toolbar.add(button);
                    part.add(button);
                }
            }
        },

        setValue : function(value, nocontentupdate)
        {
            if (this.__value != value) {
                var old = this.__value;
                this.__setContentHtml(value, nocontentupdate);
                this.fireDataEvent("changeValue", value, old);
            }
        },

        getValue : function()
        {
            if (this.__isContentReady()) {
                return this.__getContentHtml();
            }
            return "";
        },

        resetValue : function()
        {
            this.__resetContentHtml();
        },

        // SIM focus does not work, dunno why...
        focus : function()
        {
            this.getChildControl("htmlarea").focus();
        },

        __isContentReady : function()
        {
            return this.getChildControl("htmlarea").isReady();
        },

        __getContentHtml : function()
        {
            return this.getChildControl("htmlarea").getContentBody().innerHTML;
        },

        __setContentHtml : function(html, nocontentupdate)
        {
            var htmlarea = this.getChildControl("htmlarea");
            if (htmlarea.isReady() == false) {
                if (html.length > 0) {
                    htmlarea.addListenerOnce("ready", function(e){
                        this.__setContentHtml(html, nocontentupdate);
                    }, this);
                }
                return;
            }
            if (nocontentupdate !== true) {
                htmlarea.getContentBody().innerHTML = html;
            }
            this.__value = html;
            return this;
        },

        __resetContentHtml : function()
        {
            this.__setContentHtml("");
            return this;
        },

        _applyMode : function(value, old)
        {
            this.__setupToolbar();
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "toolbar":
                    control = new qx.ui.toolbar.ToolBar();
                    break;

                case "htmlarea":
                    control = new qx.ui.embed.HtmlArea().set({
                        minHeight         : 250,
                        defaultFontFamily : this.__fonts[0],
                        defaultFontSize   : 2,
                        insertParagraphOnLinebreak : false
                    });
                    control.addListener("focusOut", this.__onFocusOut, this);
                    break;
            }

            return control || this.base(arguments);
        },

        __value : "",

        __onFocusOut : function()
        {
            this.setValue(this.__getContentHtml(), true);
        },

        __fontColorHandler : function(e)
        {
            var result = window.prompt("Kolor czcionki (hex, rgb):");
            if (qx.util.ColorUtil.isValidPropertyValue(result)) {
                this.setTextColor(result);
            } else {
                alert("Nieprawidłowy kolor");
            }
        },
        
        __textBackgroundColorHandler : function(e)
        {
            var result = window.prompt("Kolor tła (hex, rgb):");
            if (qx.util.ColorUtil.isValidPropertyValue(result)) {
                this.setTextBackgroundColor(result);
            } else {
                alert("Nieprawidłowy kolor");
            }
        },

        __insertImageHandler : function(e)
        {
            var result = window.prompt("Adres URL obrazka:", "http://");
            var valid = false;
            try {
                valid = Validate.url()(result);
            } catch (ex) {
                alert(ex.message);
            }
            if (valid) {
                var attributes = {
                    src    : result,
                    border : 0
                };
                this.insertImage(attributes);
            }
        },

        __insertTableHandler : function(e)
        {
            var result = window.prompt("Liczba kolumn, liczba wierszy:", "2, 2");
            var matches = result.match(/\d+/g) || [];

            if (matches.length > 1) {
                var columnCount = matches[0] * 1;
                var rowCount    = matches[1] * 1;

                var table = '<table border="1" cellspacing="0" cellpadding="0">' + (new Array(rowCount + 1)).join('<tr>' + (new Array(columnCount + 1)).join('<td>&nbsp;</td>') + '</tr>') + '</table>';

                this.insertHtml(table);
            } else {
                alert("Nieprawidłowe dane")
            }
        },

        __insertLinkHandler : function(e)
        {
            var result = window.prompt("Adres odnośnika:", "http://");
            var valid = false;
            try {
                valid = Validate.url()(result);
            } catch (ex) {
                alert(ex.message);
            }
            if (valid) {
                this.insertHyperLink(result);
            }
        },
        
        __insertHTMLHandler : function(e)
        {
            var result = window.prompt("Kod HTML:", "");
            this.insertHtml(result);
        },

        __fonts : ["Arial", "Arial Black", "Comic Sans MS", "Courier", "Courier New", "Georgia", "Impact", "Lucida Console", "Tahoma", "Times New Roman", "Verdana"],

        __fontFamilyToolbarEntry : function()
        {
            var button = new qx.ui.form.SelectBox().set({
                toolTipText : Tools["tr"]("texteditor:fontFamily"),
                focusable   : false,
                keepFocus   : true,
                width       : 150,
                height      : 16,
                margin      : [ 4, 0 ]
            });

            button.addListener("changeSelection", function(e) {
                var value = e.getData()[0].getLabel();
                if (value != "") {
                    this.setFontFamily(value);
                }
            }, this.getChildControl("htmlarea"));

            for (var i = 0, j = this.__fonts.length; i < j; i++)
            {
                var fontName = this.__fonts[i];
                var entry = new qx.ui.form.ListItem(fontName).set({
                    focusable : false,
                    keepFocus : true,
                    font      : qx.bom.Font.fromString("12px " + fontName)
                });
                button.add(entry);
            }

            return button;
        },

        __fontSizeToolbarEntry : function()
        {
            var button = new qx.ui.form.SelectBox().set({
                toolTipText : Tools["tr"]("texteditor:fontSize"),
                focusable   : false,
                keepFocus   : true,
                width       : 50,
                height      : 16,
                margin      : [ 4, 0 ]
            });

            button.addListener("changeSelection", function(e) {
                var value = e.getData()[0].getLabel();
                if (value != "") {
                    this.setFontSize(value);
                }
            }, this.getChildControl("htmlarea"));

            for (var i = 1; i <= 7; i++)
            {
                var entry = new qx.ui.form.ListItem(i + "").set({
                    focusable : false,
                    keepFocus : true
                });
                button.add(entry);

                if (i == 2) {
                    button.setSelection([entry]);
                }
            }

            return button;
        }
    }
});