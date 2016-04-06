/* *********************************

#asset(qx/icon/${qx.icontheme}/16/actions/list-add.png)

********************************* */

qx.Class.define("frontend.app.module.course.Units",
{
    extend : qx.ui.container.Composite,

    properties :
    {
        appearance :
        {
            refine : true,
            init : "multi-items-list"
        }
    },

    construct : function()
    {
        this.base(arguments);

        this.setLayout(new qx.ui.layout.VBox(10));

        this.add(this.getChildControl("header"));
        this.add(this.getChildControl("scroller"));
        this.add(this.getChildControl("footer"));

        this._formValidator = new frontend.lib.ui.form.validation.Manager();
        this._formValidator.addListener("complete", this._onValidateComplete, this);

        this._addRow();
    },

    events :
    {
        "completed" : "qx.event.type.Event"
    },

    members :
    {
        _footer : null,

        _colWidths : [25, 200, 80, 200, 120],

        _formValidator : null,

        _items : null,

        _createChildControlImpl : function(id, hash)
        {
            var control;

            switch (id)
            {
                case "header":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(10));
                    this._addRowItems(
                        control,
                        new qx.ui.basic.Label("Lp."),
                        new qx.ui.basic.Label("nazwa jednostki"),
                        new qx.ui.basic.Label("ile modułów"),
                        new qx.ui.basic.Label("trener"),
                        null
                    );
                    break;

                case "footer":
                    control = new qx.ui.container.Composite(new qx.ui.layout.HBox(5));
                    control.add(this.getChildControl("add-button"));
                    break;

                case "add-button":
                    control = new qx.ui.basic.Atom("dodaj jednostkę", "icon/16/actions/list-add.png").set({
                        cursor : "pointer"
                    });
                    control.addListener("click", this._addRow, this);
                    break;

                case "scroller":
                    control = new qx.ui.container.Scroll(this.getChildControl("list")).set({
                        scrollbarX : "off",
                        minHeight  : 25,
                        maxHeight  : 200,
                        height     : null
                    });
                    break;

                case "list":
                    control = new qx.ui.container.Composite(new qx.ui.layout.VBox(10));
                    break;
            }

            return control || this.base(arguments, id);
        },

        send : function()
        {
            this.setEnabled(false);
            this._formValidator.reset();
            this._formValidator.validate();
        },

        _onValidateComplete : function()
        {
            var valid = this._formValidator.isValid();
            if (valid) {
                this.fireEvent("completed");
            }
            this.setEnabled(true);
        },

        getValues : function()
        {
            var result = []
            this.getUnitRows().forEach(function(unitrow){
                result.push(unitrow.getValues());
            });
            return {
                course_units : JSON.stringify(result)
            };
        },

        _addRowItems : function(container, varargs)
        {
            for (var i = 1, l = arguments.length; i < l; i++)
            {
                var item = arguments[i] || new qx.ui.basic.Label("");
                item.setWidth(this._colWidths[container.getChildren().length]);
                container.add(item);
            }
        },

        _addRow : function()
        {
            var rowCount = this.getChildControl("list").getChildren().length;
            
            var unitrow = new frontend.app.module.course.UnitRow();
            var control;
            if (rowCount == 0) {
                control = unitrow.getChildControl("add-coach-button");
                control.addListener("click", this._onAddCoachClick(unitrow), this);
            } else {
                control = unitrow.getChildControl("remove-button");
                control.addListenerOnce("click", this._onRemoveRowClick(unitrow), this);
            }
            control.show();

            this.getChildControl("list").add(unitrow);
            
            this._formValidator.add(unitrow.getChildControl("name"), [
                Validate.string(),
                Validate.slength(2, 256)
            ], this);
            this._formValidator.add(unitrow.getChildControl("hour-amount"), null, this);
            this._formValidator.add(unitrow.getChildControl("coach-id"), null, this);

            this._updateRows();

            return unitrow;
        },

        getUnitRows : function()
        {
            return this.getChildControl("list").getChildren();
        },

        _updateRows : function()
        {
            this.getUnitRows().forEach(function(unitrow, i){
                unitrow.getChildControl("lp").setValue((i + 1) + ".");
            }, this);
        },

        _removeRow : function(unitrow)
        {
            this._formValidator.remove(unitrow.getChildControl("name"));
            this._formValidator.remove(unitrow.getChildControl("hour-amount"));
            this._formValidator.remove(unitrow.getChildControl("coach-id"));

            this.getChildControl("list").remove(unitrow);
            this._updateRows();
        },

        _onRemoveRowClick : function(unitrow)
        {
            return function(e)
            {
                var dialog = new frontend.lib.dialog.Confirm("Na pewno chcesz usunąć tę pozycję?");
                dialog.addListenerOnce("yes", function (e) {
                    this._removeRow(unitrow);
                }, this);
            }
        },

        populate : function(data)
        {
            qx.lang.Array.clone(this.getUnitRows()).forEach(this._removeRow, this);
            data.forEach(function(dataEntry){
                this._addRow().populate(dataEntry);
            }, this);
        },

        getFormValidator : function()
        {
            return this._formValidator;
        },

        _onAddCoachClick : function(unitrow)
        {
            return function(e)
            {
                var manager = new frontend.app.module.user.Manager(5);
                manager.addListener("completed", function(e){
                    unitrow.getChildControl("coach-id").resetValue();
                }, this);
                manager.open();
            }
        }
    }
});
