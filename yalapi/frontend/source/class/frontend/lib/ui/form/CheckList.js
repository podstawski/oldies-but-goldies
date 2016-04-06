qx.Class.define("frontend.lib.ui.form.CheckList",
{
    extend : qx.ui.form.List,

    include : [
        frontend.lib.ui.form.MSourceProperty
    ],

    construct : function()
    {
        this.base(arguments);

        this.setHeight(null);
        this.setMaxHeight(200);
        
        this.__controller = new qx.data.controller.List(null, this).set({
            delegate : {
                createItem : function() {
                    return new frontend.lib.ui.form.CheckBox;
                },
                configureItem : function(item) {
                    item.setPadding(3);
                },
                bindItem : function(controller, item, id)
                {
                    controller.bindProperty("label", "label", null, item, id);
                    controller.bindProperty("value", "value", null, item, id);
                    controller.bindPropertyReverse("value", "value", null, item, id);
                }
            }
        });
    },

    members :
    {
        _applySource : function(source, old)
        {
            if (old) {
                old.removeListener("changeData", this._updateData, this);
            }

            if (source) {
                source.addListener("changeData", this._updateData, this);
                this._updateData();
            }
        },

        _updateData : function()
        {
            var key  = this.getSourceDataKey();
            var data = this.getSourceData();

            var model = new qx.data.Array;

            if (data) {
                data.forEach(function(dataEntry){
                    model.push(
                        new frontend.lib.ui.form.CheckListItem().set({
                            id    : dataEntry.id,
                            label : dataEntry[key] || dataEntry.label,
                            value : dataEntry.value === true
                        })
                    );
                }, this);
            }

            this.getController().setModel(model);
        },

        getController : function()
        {
            return this.__controller;
        },

        getCheckedIds : function()
        {
            var checkedIds = [];
            var model = this.getController().getModel();
            if (model) {
                model.forEach(function(item){
                    if (item.getValue() == true) {
                        checkedIds.push(
                            item.getId()
                        );
                    }
                }, this);
            }
            return checkedIds;
        },

        getValues : function()
        {
            var values = {};
            var model = this.getController().getModel();
            if (model) {
                model.forEach(function(item, name) {
                    values[name] = item.getValue() == true ? 1 : 0;
                }, this);
            }
            return values;
        }
    }
});

qx.Class.define("frontend.lib.ui.form.CheckListItem",
{
    extend : qx.core.Object,

    events :
    {
        "changeId" : "qx.event.type.Data",
        "changeLabel" : "qx.event.type.Data",
        "changeValue" : "qx.event.type.Data"
    },

    properties :
    {
        id :
        {
            check : "Integer",
            event : "changeId"
        },
        
        label :
        {
            check : "String",
            event : "changeLabel"
        },

        value :
        {
            check : "Boolean",
            init : false,
            event : "changeValue"
        }
    }
});