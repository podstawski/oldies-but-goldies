qx.Class.define("frontend.lib.ui.form.renderer.Single",
{
    extend : qx.ui.form.renderer.AbstractRenderer,

    construct : function(form)
    {
        var layout = new qx.ui.layout.Grid();
        layout.setSpacing(10);
        layout.setColumnFlex(1, 1);
        layout.setColumnAlign(0, "left", "top");
        this._setLayout(layout);

        this.base(arguments, form);

        form.addListener("validate", this._onValidateStart, this);
        form.addListener("formValid", this._onValidateEnd, this);
        form.addListener("formNotValid", this._onValidateEnd, this);
    },

    members :
    {
        _row : 0,
        _buttonRow : null,

        _onValidateStart : function()
        {
            this.setEnabled(false);
        },

        _onValidateEnd : function()
        {
            this.setEnabled(true);
        },

        // SIM overriden
        addItems : function(items, names, title)
        {
            if (title != null) {
                this._add(
                    this._createHeader(title), {row: this._row, column: 0, colSpan: 2}
                );
                this._row++;
            }

            for (var i = 0; i < items.length; i++)
            {
                var item = items[i];
                var label = this._createLabel(names[i], items[i]).set({paddingTop:4});
                
                if (qx.Class.isSubClassOf(item.constructor, frontend.lib.ui.form.TinyMCE)) {
                    this._getLayout().setRowFlex(this._row, 1);
                    this._add(item, {row: this._row, column: 0, colSpan: 2});
                }
                else if (qx.Class.isSubClassOf(item.constructor, frontend.lib.ui.form.Attachment)) {
                    this._add(label, {row: this._row, column: 0});
                    this._add(item, {row: this._row, column: 1, rowSpan: 2});
                    this._row++;
                    var previewContainer = new frontend.lib.ui.container.PreviewImages();
                    item.setPreviewContainer(previewContainer);
                    this._add(previewContainer, {row: this._row, column: 0});
                    label.setBuddy(item);
                    this._connectVisibility(item, label);
                }
                else {
                    this._add(label, {row: this._row, column: 0});
                    this._add(item, {row: this._row, column: 1});
                    label.setBuddy(item);
                    this._connectVisibility(item, label);
                }
                this._row++;

                if (qx.core.Environment.get("qx.dynlocale")) {
                    this._names.push({name: names[i], label: label, item: items[i]});
                }
            }
        },

        addButton : function(button)
        {
            if (this._buttonRow == null) {
                this._buttonRow = new qx.ui.container.Composite();
                this._buttonRow.setMarginTop(5);
                var hbox = new qx.ui.layout.HBox();
                hbox.setAlignX("right");
                hbox.setSpacing(5);
                this._buttonRow.setLayout(hbox);
                this._add(this._buttonRow, {row: this._row, column: 0, colSpan: 2});
                this._row++;
            }

            // add the button
            this._buttonRow.add(button);
        },

        getLayout : function()
        {
            return this._getLayout();
        },

        _createLabel : function(name, item)
        {
            var label = new qx.ui.basic.Label(this._createLabelText(name, item));
            label.setRich(true);
            return label;
        },

        _createHeader : function(title)
        {
            var header = new qx.ui.basic.Label(title);
            header.setFont("bold");
            if (this._row != 0) {
                header.setMarginTop(10);
            }
            header.setAlignX("left");
            return header;
        }
    }
});