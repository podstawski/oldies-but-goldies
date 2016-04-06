/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/go-previous.png)
#asset(qx/icon/${qx.icontheme}/16/actions/go-next.png)
#asset(qx/icon/${qx.icontheme}/16/actions/edit-delete.png)
#asset(qx/icon/${qx.icontheme}/16/actions/zoom-in.png)

********************************* */

qx.Class.define("frontend.lib.ui.container.PreviewImages",
{
    extend : qx.ui.container.Composite,

    events :
    {
        "changeImages" : "qx.event.type.Event",
        "changeMaxImageWidth" : "qx.event.type.Data",
        "changeMaxImageHeight" : "qx.event.type.Data"
    },
    
    properties :
    {
        images :
        {
            check : "Array",
            nullable : false,
            init : [],
            apply : "_applyImages",
            transform : "_transformImages",
            event : "changeImages"
        },

        maxImageWidth :
        {
            check : "PositiveInteger",
            nullable : false,
            init : 100,
            event : "changeMaxImageWidth"
        },

        maxImageHeight :
        {
            check : "PositiveInteger",
            nullable : false,
            init : 100,
            event : "changeMaxImageHeight"
        },

        maxImageSize :
        {
            group : [ "maxImageWidth", "maxImageHeight" ],
            mode : "shorthand"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox);
        
        this._stack = new frontend.lib.ui.container.Stack().set({
            rotationEnabled:true
        });

        this._createButtons();
        this._createPopup();

        this.add(this._stack);
        this.add(this._buttons);

        this.addListener("changeImages", this._onChangeImages, this);

        this._onChangeImages();
    },

    members :
    {
        _stack : null,

        _buttons : null,

        _popup : null,
        
        _transformImages : function(images)
        {
            var tImages = [];
            images.forEach(function(imageEntry){
                if (qx.lang.Type.isString(imageEntry) && this.__isHash(imageEntry)) {
                    tImages.push(imageEntry);
                }
                else if (imageEntry["hash"] !== undefined && this.__isHash(imageEntry["hash"])) {
                    tImages.push(imageEntry["hash"]);
                }
            }, this);
            return tImages;
        },

        __isHash : function(hash)
        {
            return /[0-9a-z]{40}/.test(hash);
        },

        _connectVisibility : function(source, target)
        {
            source.bind("visibility", target, "visibility");
        },

        _applyImages : function(images)
        {
            this._stack.hasChildren() && this._stack.removeAll();

            images.forEach(function(imageHash){
                var image = new qx.ui.basic.Image(Urls.resolve("IMAGE_VIEW", {id:imageHash})).set({scale:true});
                this.bind("maxImageWidth", image, "maxWidth");
                this.bind("maxImageHeight", image, "maxHeight");
                this._stack.add(image);
            }, this);
        },

        _createButtons : function()
        {
            if (this._buttons === null)
            {
                this._buttons = new qx.ui.container.Composite(new qx.ui.layout.HBox(10, "center")).set({marginTop:10});
                var prev   = this._getIcon("go-previous");
                var next   = this._getIcon("go-next");
                var zoom   = this._getIcon("zoom-in");
                var remove = this._getIcon("edit-delete");

                prev.addListener("execute", this._onPrevClick, this);
                next.addListener("execute", this._onNextClick, this);
                zoom.addListener("execute", this._onZoomClick, this);
                remove.addListener("execute", this._onRemoveClick, this);

                this.addListener("changeImages", this._updateButtons(prev, next), this);

                this._buttons.add(prev);
                this._buttons.add(zoom);
                this._buttons.add(remove);
                this._buttons.add(next);
            }
        },

        _createPopup : function()
        {
            if (this._popup === null)
            {
                this._popup = new frontend.lib.ui.popup.PreviewImage();
            }
        },

        _getIcon : function(image)
        {
            var icon = new qx.ui.basic.Image("icon/16/actions/" + image + ".png").set({
                appearance : "preview-images-icon",
                toolTipText : Tools["tr"]("_previewImageIcon_:" + image)
            });
            return icon;
        },

        _updateButtons : function()
        {
            var buttons = arguments || [];
            
            return function(e) {
                var visibility = this.getImages().length > 1 ? "visible" : "hidden";
                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].setVisibility(visibility);
                }
            }
        },

        _onChangeImages : function()
        {
            var visibility = this.getImages().length > 0 ? "visible" : "excluded";
            this.setVisibility(visibility);
        },

        _onPrevClick : function()
        {
            this._stack.previous();
        },

        _onNextClick : function()
        {
            this._stack.next();
        },

        _onZoomClick : function()
        {
            var imageUrl = this._stack.getChildren()[this._stack.getSelectedIndex()].getSource();
            this._popup.setImageUrl(imageUrl);
            this._popup.center();
            this._popup.open();
        },

        _onRemoveClick : function()
        {
            alert("TODO: _onRemoveClick");
        }
    }
});