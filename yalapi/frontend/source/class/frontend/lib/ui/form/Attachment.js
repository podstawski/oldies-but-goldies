qx.Class.define("frontend.lib.ui.form.Attachment",
{
    extend : qx.ui.container.Composite,

    implement : [
        qx.ui.form.IForm,
        frontend.lib.ui.form.IFile
    ],

    include : [
        qx.ui.form.MForm
    ],

    events :
    {
        "changeMaxItems" : "qx.event.type.Data"
    },

    properties :
    {
        maxItems :
        {
            check : "PositiveInteger",
            nullable : false,
            init : 3,
            event : "changeMaxItems"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox(5));

        this.setRequiredInvalidMessage("Proszę wybrać co najmniej jeden plik");

        this.addListener("changeValid", this._onChangeValid, this);

        this._items = {};

        this._addItem();
    },

    members :
    {
        _lastID : 0,

        _items : null,

        _hidden : null,

        _addItem : function()
        {
            var container = new qx.ui.container.Composite(new qx.ui.layout.HBox);
            var item  = new frontend.lib.ui.form.File();
            var aIcon = new qx.ui.basic.Image("icon/16/actions/list-add.png").set({
                appearance : "attachment-icon",
                toolTipText : "Dodaj nowy załącznik"
            });
            var rIcon = new qx.ui.basic.Image("icon/16/actions/list-remove.png").set({
                appearance : "attachment-icon",
                toolTipText : "Usuń ten załącznik"
            });
            var index = this._lastID++;

            container.add(item, {flex: 1});
            container.add(aIcon);
            container.add(rIcon);

            container.addListener("resize", this._onContainerResize(index), this);
            aIcon.addListener("click", this._addItem, this);
            rIcon.addListener("click", this._removeItem(index), this);

            this.add(container);

            var input = item._createChildControl("hiddeninput");
            input.name = "file[" + index + "]";

            var element = new qx.html.Element();
            element.useElement(input);

            this.getContentElement().add(element);

            this._items[index] =
            {
                container : container,
                item : item,
                aIcon : aIcon,
                rIcon : rIcon,
                input : input,
                element : element
            }

            this._updateItems();
        },

        _removeItem : function(index)
        {
            return function()
            {
                this.remove(this._items[index].container);
                this.getContentElement().remove(this._items[index].element);
                delete this._items[index];
                this._updateItems();
            }
        },

        _onAIconClick : function(e)
        {
            this._addItem();
        },

        _onRIconClick : function(index)
        {
            return function(e) {
                this._removeItem(index);
            }
        },

        _updateItems : function()
        {
            var maxItems   = this.getMaxItems();
            var itemsCount = this.getChildren().length;

            for (var index in this._items) {
                this._items[index].aIcon.setVisibility(itemsCount < maxItems ? "visible" : "hidden");
                this._items[index].rIcon.setVisibility(index > 0 ? "visible" : "hidden");
            }
        },

        _onContainerResize : function(index)
        {
            return function(e)
            {
                var data = e.getData();
                this._items[index].input.style.top = data.top + "px";
            }
        },

        _onChangeValid : function(e)
        {
            var valid = e.getData();
            this._items[0].item.setValid(valid);
        },

        // SIM overriden
        _createChildControl : function(id)
        {
            return null;
        },

        getFileName : function()
        {
            return this._items[0].input.value;
        }
    }
});