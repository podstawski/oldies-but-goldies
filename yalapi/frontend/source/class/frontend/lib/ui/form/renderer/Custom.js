qx.Class.define("frontend.lib.ui.form.renderer.Custom",
{
    extend : qx.ui.form.renderer.AbstractRenderer,

    construct : function(form)
    {
        this._nextColumn = [];

        var layout = new qx.ui.layout.Grid();
        layout.setSpacing(6);
        this._setLayout(layout);

        this.base(arguments, form);
    },

    members :
    {
        _nextColumn : null,

        _getNextColumn : function(rowIndex)
        {
            this._nextColumn[rowIndex] = this._nextColumn[rowIndex] || 0;
            return this._nextColumn[rowIndex]++;
        },
        
        addItems : function(items, names, title, options)
        {
            var row, col, item, name, label;
            var layout = this._getLayout();
            
            for (var i = 0; i < items.length; i++)
            {
                qx.core.Assert.assertKeyInMap("row", options[i]);

                row  = options[i]["row"];
                item = items[i];
                name = names[i];
                
                if (name)
                {
                    col  = this._getNextColumn(row);

                    label = this._createLabel(name, item);
                    this._add(label, {row: row, column: col});
                    layout.setColumnAlign(col, "left", "middle");

                    label.setBuddy(item);

                    this._connectVisibility(item, label);

                    if (qx.core.Environment.get("qx.dynlocale")) {
                        this._names.push({name: name, label: label, item: item});
                    }
                }
                
                col = this._getNextColumn(row);
                this._add(item, {row: row, column: col});
//                layout.setColumnFlex(col, 1);
            }
        },

        addButton : function(button)
        {
            
        },

        _createLabel : function(name, item)
        {
            var label = new qx.ui.basic.Label(this._createLabelText(name, item));
            label.setRich(true);
            return label;
        }
    }
});