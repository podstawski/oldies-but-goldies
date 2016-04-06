qx.Class.define("frontend.lib.ui.table.headerrenderer.CheckboxCell",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "table-header-cell"
        },

        label :
        {
            check : "String",
            init : null,
            nullable : true,
            apply : "_applyLabel"
        },

        sortIcon :
        {
            check : "String",
            init : null,
            nullable : true,
            apply : "_applySortIcon",
            themeable : true
        }
    },

    construct : function(cellInfo)
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.HBox(2, "center"));

        this.__cellInfo = qx.lang.Object.clone(cellInfo);
        
        this.add(this.getChildControl("checkbox"));
        this.add(this.getChildControl("label"), {flex:1});
        this.add(this.getChildControl("sort-icon"));
    },

    members :
    {
        __cellInfo : null,

        _applySortIcon : function(value, old)
        {
            if (value) {
                this._showChildControl("sort-icon").setSource(value);
            } else {
                this._excludeChildControl("sort-icon");
            }
        },

        _applyLabel : function(value, old)
        {
            if (value) {
                this._showChildControl("label").setValue(value);
            } else {
                this._excludeChildControl("label");
            }
        },

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "checkbox":
                    control = new frontend.lib.ui.form.CheckBox().set({
                        anonymous    : false,
                        allowShrinkX : true,
                        triState     : true,
                        value        : false,
                        executable   : false
                    });
                    control.addListener("execute", this._onCheckboxClick, this);
                    control.bind("value", control, "toolTipText", {
                        converter : function(value){
                            if (value == true) {
                                return "odznacz widoczne rekordy";
                            } else if (value == false || value == null) {
                                return "zaznacz widoczne rekordy"
                            } else {
                                return null;
                            }
                        }
                    });
                    break;

                case "label":
                    control = new qx.ui.basic.Label().set({
                        anonymous    : true,
                        allowShrinkX : true,
                        rich         : true
                    });
                    break;

                case "sort-icon":
                  control = new qx.ui.basic.Image().set({
                      anonymous : true
                  });
                  break;
            }

            return control || this.base(arguments, id);
        },

        _onCheckboxClick : function(e)
        {
            var checked = !this.getChildControl("checkbox").getValue();
            this.__cellInfo.table.fireDataEvent("headerCheckboxClick", {
                col : this.__cellInfo.col,
                checked : checked
            });
            this.getChildControl("checkbox").setValue(checked);
        }
    }
});